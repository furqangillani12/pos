<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Refund;
use App\Models\CreditLedger;
use App\Models\CreditTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;

class PosController extends Controller
{
    public function index()
    {
        $customers  = Customer::get();
        $categories = Category::all();

        return view('admin.pos.index', [
            'customers'  => $customers,
            'categories' => $categories,
            'tax_rate'   => config('pos.tax_rate'),
        ]);
    }

    public function searchProducts(Request $request)
    {
        $query = Product::with(['unit']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('created_at', 'desc')
                          ->paginate($request->input('per_page', 30));

        return response()->json([
            'data' => $products->map(function ($p) {
                return [
                    'id'              => $p->id,
                    'name'            => $p->name,
                    'barcode'         => $p->barcode,
                    'sale_price'      => $p->sale_price,
                    'resale_price'    => $p->resale_price,
                    'wholesale_price' => $p->wholesale_price,
                    'weight'          => $p->weight ?? 0,
                    'unit'            => $p->unit->abbreviation ?? '',
                    'category_id'     => $p->category_id,
                    'stock_quantity'  => $p->stock_quantity,
                    'reorder_level'   => $p->reorder_level ?? 5,
                    'rank'            => $p->rank,
                    'image'           => $p->image ? asset('storage/' . $p->image) : null,
                ];
            }),
            'current_page' => $products->currentPage(),
            'last_page'    => $products->lastPage(),
            'total'        => $products->total(),
        ]);
    }

    public function storeOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id'        => 'nullable|exists:customers,id',
                'items'              => 'required|array',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity'   => 'required|numeric|min:0.01',
                'items.*.unit_price' => 'nullable|numeric|min:0',
                'payment_method'     => 'required|string',
                'notes'              => 'nullable|string|max:1000',
                'paid_amount'        => 'nullable|numeric|min:0',   // ← NEW: partial payment
                'dispatch_method'    => 'nullable|string',
                'tracking_id'        => 'nullable|string',
                'delivery_charges'   => 'nullable|numeric|min:0',
                'tax_rate'           => 'nullable|numeric|min:0',
                'discount'           => 'nullable|numeric|min:0',
            ]);

            DB::beginTransaction();

            $customer     = null;
            $customerType = 'walkin';

            if (!empty($validated['customer_id'])) {
                $customer     = Customer::findOrFail($validated['customer_id']);
                $customerType = $customer->customer_type ?? 'customer';
            }

            $totalWeight = 0;
            $subtotal    = 0;
            $orderItems  = [];

            foreach ($validated['items'] as $item) {
                $product   = Product::findOrFail($item['product_id']);
                $unitPrice = !empty($item['unit_price'])
                    ? (float) $item['unit_price']
                    : $this->getPriceForCustomerType($product, $customerType);
                $itemTotal = $unitPrice * $item['quantity'];
                $subtotal += $itemTotal;

                if (!empty($product->weight)) {
                    $totalWeight += $product->weight * $item['quantity'];
                }

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Not enough stock for {$product->name}");
                }

                $orderItems[] = [
                    'product'    => $product,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total'      => $itemTotal,
                ];
            }

            // ── Calculate totals ─────────────────────────────────────────────
            $taxRate         = $validated['tax_rate'] ?? config('pos.tax_rate', 0);
            $discount        = $validated['discount'] ?? 0;
            $deliveryCharges = $validated['delivery_charges'] ?? 0;
            $afterDiscount   = $subtotal - $discount;
            $tax             = $afterDiscount * ($taxRate / 100);
            $total           = $afterDiscount + $tax + $deliveryCharges;

            // ── Partial payment logic ────────────────────────────────────────
            // paid_amount defaults to full total (fully paid)
            $paidAmount = isset($validated['paid_amount']) && $validated['paid_amount'] !== null
                ? (float) $validated['paid_amount']
                : $total;

            // Cap paid amount — can't pay more than total on THIS bill
            // (extra advance will be handled as negative balance = credit)
            $previousBalance = 0;
            if ($customer) {
                $previousBalance = (float) ($customer->current_balance ?? 0);
            }

            // Balance on this order = what customer still owes FOR THIS ORDER
            $balanceOnOrder = max(0, $total - $paidAmount);

            // New running balance = previous balance + this order's remaining
            // If customer overpaid (paidAmount > total), the extra reduces their balance
            $newRunningBalance = $previousBalance + $total - $paidAmount;

            // ── Determine payment status ─────────────────────────────────────
            $paymentStatus = $balanceOnOrder <= 0 ? 'paid' : 'partial';

            // ── Create order ─────────────────────────────────────────────────
            $order = Order::create([
                'order_number'     => Order::generateOrderNumber(),
                'customer_id'      => $customer ? $customer->id : null,
                'customer_type'    => $customerType,
                'user_id'          => auth()->id(),
                'order_type'       => 'pos',
                'payment_method'   => $validated['payment_method'],
                'status'           => 'completed',
                'notes'            => $validated['notes'] ?? null,
                'tax_rate'         => $taxRate,
                'tax'              => $tax,
                'dispatch_method'  => $validated['dispatch_method'] ?? null,
                'tracking_id'      => $validated['tracking_id'] ?? null,
                'discount'         => $discount,
                'delivery_charges' => $deliveryCharges,
                'weight'           => $totalWeight,
                'subtotal'         => $subtotal,
                'total'            => $total,
                // ── NEW FIELDS ──
                'paid_amount'      => $paidAmount,
                'previous_balance' => $previousBalance,
                'balance_amount'   => $balanceOnOrder,
            ]);

            // ── Create order items & update stock ─────────────────────────────
            foreach ($orderItems as $itemData) {
                $product = $itemData['product'];

                OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $product->id,
                    'quantity'    => $itemData['quantity'],
                    'unit_price'  => $itemData['unit_price'],
                    'total_price' => $itemData['total'],
                ]);

                if ($product->track_inventory) {
                    $product->decrement('stock_quantity', $itemData['quantity']);
                }
            }

            // ── Create payment record ─────────────────────────────────────────
            if ($customer && $paidAmount > 0) {
                Payment::create([
                    'payment_number'   => Payment::generatePaymentNumber(),
                    'order_id'         => $order->id,
                    'customer_id'      => $customer->id,
                    'amount'           => $paidAmount,
                    'payment_date'     => now(),
                    'payment_method'   => $validated['payment_method'],
                    'reference_number' => $validated['tracking_id'] ?? null,
                    'status'           => 'completed',
                    'created_by'       => auth()->id(),
                ]);
            }

            // ── Update customer running balance ───────────────────────────────
            if ($customer) {
                $customer->current_balance = $newRunningBalance;
                $customer->save();
            }

            DB::commit();

            $order->load('items');

            return response()->json([
                'success'          => true,
                'message'          => 'Order created successfully',
                'order_id'         => $order->id,
                'order_number'     => $order->order_number,
                'total'            => $order->total,
                'paid_amount'      => $paidAmount,
                'balance_amount'   => $balanceOnOrder,
                'previous_balance' => $previousBalance,
                'new_balance'      => $newRunningBalance,
                'receipt_url'      => route('admin.pos.receipt', $order),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper method to get price based on customer type
     */
    private function getPriceForCustomerType($product, $customerType)
    {
        switch ($customerType) {
            case 'reseller':
                return $product->resale_price ?? $product->sale_price;
            case 'wholesale':
                return $product->wholesale_price ?? $product->sale_price;
            default:
                return $product->sale_price;
        }
    }

    /**
     * Process credit sale - called from CreditController
     */
    public function processCreditSale(Request $request, Order $order)
    {
        try {
            DB::beginTransaction();

            $customer = Customer::findOrFail($order->customer_id);

            if (!$customer->credit_enabled) {
                throw new \Exception('Credit is not enabled for this customer');
            }

            $ledger = $customer->creditLedger;

            if (!$ledger) {
                $ledger = CreditLedger::create([
                    'ledger_number'   => CreditLedger::generateLedgerNumber(),
                    'customer_id'     => $customer->id,
                    'total_debit'     => 0,
                    'total_credit'    => 0,
                    'opening_balance' => 0,
                    'closing_balance' => 0,
                    'credit_limit'    => $customer->credit_limit,
                    'status'          => 'active',
                    'notes'           => 'Credit enabled on ' . now()->format('Y-m-d'),
                ]);
            }

            $balanceBefore = $customer->current_balance;
            $balanceAfter  = $balanceBefore + $order->total;
            $dueDate       = now()->addDays($customer->credit_due_days ?? 30);

            CreditTransaction::create([
                'transaction_number' => CreditTransaction::generateTransactionNumber(),
                'credit_ledger_id'   => $ledger->id,
                'customer_id'        => $customer->id,
                'order_id'           => $order->id,
                'transaction_type'   => 'debit',
                'amount'             => $order->total,
                'balance_before'     => $balanceBefore,
                'balance_after'      => $balanceAfter,
                'reference_number'   => $order->order_number,
                'description'        => 'Credit purchase - Order #' . $order->order_number,
                'transaction_date'   => now(),
                'due_date'           => $dueDate,
                'payment_status'     => 'pending',
                'items'              => $order->items->map(function ($item) {
                    return [
                        'product_id'   => $item->product_id,
                        'product_name' => $item->product->name ?? null,
                        'quantity'     => $item->quantity,
                        'price'        => $item->unit_price,
                        'total'        => $item->total_price,
                    ];
                }),
                'paid_amount'      => 0,
                'remaining_amount' => $order->total,
                'created_by'       => auth()->id(),
            ]);

            $ledger->total_debit           += $order->total;
            $ledger->closing_balance        = $balanceAfter;
            $ledger->last_transaction_date  = now();
            $ledger->save();

            $customer->current_balance = $balanceAfter;
            $customer->save();

            $order->update([
                'payment_method'           => 'credit',
                'credit_status'            => 'pending',
                'credit_ledger_id'         => $ledger->id,
                'credit_due_date'          => $dueDate,
                'credit_paid_amount'       => 0,
                'credit_remaining_amount'  => $order->total,
            ]);

            DB::commit();

            return response()->json([
                'success'     => true,
                'message'     => 'Credit sale processed successfully',
                'transaction' => $order,
                'balance'     => $balanceAfter,
                'due_date'    => $dueDate->format('Y-m-d'),
                'amount'      => $order->total,
                'receipt_url' => route('admin.pos.receipt', $order->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process credit sale: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function editOrder(Order $order)
    {
        $order->load('items.product', 'customer');
        $products   = Product::with(['variants', 'category', 'unit'])->orderBy('created_at', 'desc')->get();
        $customers  = Customer::get();
        $categories = Category::all();

        return view('admin.pos.edit', [
            'order'      => $order,
            'products'   => $products,
            'customers'  => $customers,
            'categories' => $categories,
            'tax_rate'   => config('pos.tax_rate'),
        ]);
    }

    public function updateOrder(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
                'customer_id'        => 'nullable|exists:customers,id',
                'items'              => 'required|array',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity'   => 'required|numeric|min:0.01',
                'items.*.unit_price' => 'nullable|numeric|min:0',
                'payment_method'     => 'required|string',
                'paid_amount'        => 'nullable|numeric|min:0',
                'dispatch_method'    => 'nullable|string',
                'tracking_id'        => 'nullable|string',
                'delivery_charges'   => 'nullable|numeric|min:0',
                'tax_rate'           => 'nullable|numeric|min:0',
                'discount'           => 'nullable|numeric|min:0',
                'notes'              => 'nullable|string|max:1000',
            ]);

            DB::beginTransaction();

            // Restore old stock
            foreach ($order->items as $oldItem) {
                if ($oldItem->product && $oldItem->product->track_inventory) {
                    $oldItem->product->increment('stock_quantity', $oldItem->quantity);
                }
            }

            // Remove old items
            $order->items()->delete();

            $customer     = null;
            $customerType = 'walkin';

            if (!empty($validated['customer_id'])) {
                $customer     = Customer::findOrFail($validated['customer_id']);
                $customerType = $customer->customer_type ?? 'customer';
            }

            // Reverse old balance effect on customer
            $oldCustomer = $order->customer;
            if ($oldCustomer) {
                $oldCustomer->current_balance = $oldCustomer->current_balance - $order->total + $order->paid_amount;
                $oldCustomer->save();
            }

            // Refresh customer model so it picks up the reversed balance
            if ($customer && $oldCustomer && $customer->id === $oldCustomer->id) {
                $customer->refresh();
            }

            $totalWeight = 0;
            $subtotal    = 0;
            $orderItems  = [];

            foreach ($validated['items'] as $item) {
                $product   = Product::findOrFail($item['product_id']);
                $unitPrice = !empty($item['unit_price'])
                    ? (float) $item['unit_price']
                    : $this->getPriceForCustomerType($product, $customerType);
                $itemTotal = $unitPrice * $item['quantity'];
                $subtotal += $itemTotal;

                if (!empty($product->weight)) {
                    $totalWeight += $product->weight * $item['quantity'];
                }

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Not enough stock for {$product->name}");
                }

                $orderItems[] = [
                    'product'    => $product,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total'      => $itemTotal,
                ];
            }

            $taxRate         = $validated['tax_rate'] ?? config('pos.tax_rate', 0);
            $discount        = $validated['discount'] ?? 0;
            $deliveryCharges = $validated['delivery_charges'] ?? 0;
            $afterDiscount   = $subtotal - $discount;
            $tax             = $afterDiscount * ($taxRate / 100);
            $total           = $afterDiscount + $tax + $deliveryCharges;

            $paidAmount = isset($validated['paid_amount']) && $validated['paid_amount'] !== null
                ? (float) $validated['paid_amount']
                : $total;

            $previousBalance = 0;
            if ($customer) {
                $previousBalance = (float) ($customer->current_balance ?? 0);
            }

            $balanceOnOrder    = max(0, $total - $paidAmount);
            $newRunningBalance = $previousBalance + $total - $paidAmount;

            $order->update([
                'customer_id'      => $customer ? $customer->id : null,
                'customer_type'    => $customerType,
                'payment_method'   => $validated['payment_method'],
                'notes'            => $validated['notes'] ?? null,
                'tax_rate'         => $taxRate,
                'tax'              => $tax,
                'dispatch_method'  => $validated['dispatch_method'] ?? null,
                'tracking_id'      => $validated['tracking_id'] ?? null,
                'discount'         => $discount,
                'delivery_charges' => $deliveryCharges,
                'weight'           => $totalWeight,
                'subtotal'         => $subtotal,
                'total'            => $total,
                'paid_amount'      => $paidAmount,
                'previous_balance' => $previousBalance,
                'balance_amount'   => $balanceOnOrder,
            ]);

            foreach ($orderItems as $itemData) {
                $product = $itemData['product'];

                OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $product->id,
                    'quantity'    => $itemData['quantity'],
                    'unit_price'  => $itemData['unit_price'],
                    'total_price' => $itemData['total'],
                ]);

                if ($product->track_inventory) {
                    $product->decrement('stock_quantity', $itemData['quantity']);
                }
            }

            if ($customer) {
                $customer->current_balance = $newRunningBalance;
                $customer->save();
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Order updated successfully',
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'total'        => $order->total,
                'receipt_url'  => route('admin.pos.receipt', $order),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function downloadReceiptPdf(Order $order)
    {
        $pdf = Pdf::loadView('admin.pos.receipt-pdf', compact('order'));
        return $pdf->download("Receipt-{$order->order_number}.pdf");
    }

    public function downloadReceipt(Order $order)
    {
        $pdf = Pdf::loadView('admin.pos.receipt', compact('order'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('receipt-' . $order->order_number . '.pdf');
    }

    public function showReceipt(Order $order)
    {
        return view('admin.pos.receipt', compact('order'));
    }

    public function processRefund(Request $request, Order $order)
    {
        if (!$order->isRefundable()) {
            return back()->with('error', 'This order cannot be refunded');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $order->total,
            'reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($request, $order) {
            Refund::create([
                'order_id' => $order->id,
                'user_id'  => auth()->id(),
                'amount'   => $request->amount,
                'reason'   => $request->reason,
                'status'   => 'completed',
            ]);

            $order->update([
                'status' => $request->amount >= $order->total
                    ? Order::STATUS_REFUNDED
                    : Order::STATUS_COMPLETED,
            ]);

            // Adjust customer balance on refund
            if ($order->customer_id) {
                $customer = Customer::find($order->customer_id);
                if ($customer) {
                    $customer->current_balance = max(0, $customer->current_balance - $request->amount);
                    $customer->save();
                }
            }

            if ($request->has('return_to_inventory')) {
                foreach ($order->items as $item) {
                    if ($item->product && $item->product->track_inventory) {
                        $item->product->increment('stock_quantity', $item->quantity);

                        $item->product->inventoryLogs()->create([
                            'action'          => 'refund_return',
                            'quantity_change' => $item->quantity,
                            'notes'           => 'Restocked due to refund of Order #' . $order->order_number,
                            'user_id'         => auth()->id(),
                        ]);
                    }
                }
            }
        });

        return back()->with('success', 'Refund processed successfully');
    }

    public function cancelOrder(Order $order)
    {
        if ($order->status === Order::STATUS_CANCELLED) {
            return back()->with('error', 'Order is already cancelled.');
        }

        DB::transaction(function () use ($order) {
            // Restore stock
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
            }

            // Adjust customer balance
            if ($order->customer_id && $order->balance_amount > 0) {
                $customer = Customer::find($order->customer_id);
                if ($customer) {
                    $customer->current_balance = max(0, $customer->current_balance - $order->balance_amount);
                    $customer->save();
                }
            }

            $order->update(['status' => Order::STATUS_CANCELLED]);
        });

        return back()->with('success', 'Order #' . $order->order_number . ' has been cancelled and stock restored.');
    }

    public function deleteOrder(Order $order)
    {
        DB::transaction(function () use ($order) {
            // Restore stock if order was not already cancelled
            if ($order->status !== Order::STATUS_CANCELLED && $order->status !== Order::STATUS_REFUNDED) {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock_quantity', $item->quantity);
                    }
                }
            }

            // Adjust customer balance
            if ($order->customer_id && $order->balance_amount > 0 && $order->status !== Order::STATUS_CANCELLED) {
                $customer = Customer::find($order->customer_id);
                if ($customer) {
                    $customer->current_balance = max(0, $customer->current_balance - $order->balance_amount);
                    $customer->save();
                }
            }

            // Delete related records
            $order->items()->delete();
            $order->payments()->delete();
            $order->delete();
        });

        return redirect()->route('admin.reports.sales')->with('success', 'Order deleted successfully.');
    }
}