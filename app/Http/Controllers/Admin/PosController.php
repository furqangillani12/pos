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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::with(['variants', 'category'])->get();
        $customers = Customer::get();
        $categories = Category::all();

        return view('admin.pos.index', [
            'products' => $products,
            'customers' => $customers,
            'categories' => $categories,
            'tax_rate' => config('pos.tax_rate'),

        ]);
    }

    public function storeOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id'        => 'nullable|exists:customers,id',
                'items'              => 'required|array',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity'   => 'required|integer|min:1',
                'payment_method'     => 'required',
                'notes'              => 'nullable|string',
                'dispatch_method'    => 'nullable|string|max:255',
                'tracking_id'        => 'nullable|string|max:255',
                'tax_rate'           => 'nullable|numeric|min:0',
                'discount'           => 'nullable|numeric|min:0',
                'delivery_charges'   => 'nullable|numeric|min:0',
            ]);

            return DB::transaction(function () use ($validated) {
                $customer = null;
                $customerType = 'customer';
                if (!empty($validated['customer_id'])) {
                    $customer = Customer::find($validated['customer_id']);
                    $customerType = $customer->customer_type ?? 'customer';
                }

                $totalWeight = 0; // ✅ initialize total weight

                $order = Order::create([
                    'order_number'     => Order::generateOrderNumber(),
                    'customer_id'      => $validated['customer_id'] ?? null,
                    'user_id'          => auth()->id(),
                    'order_type'       => 'pos',
                    'payment_method'   => $validated['payment_method'],
                    'status'           => Order::STATUS_COMPLETED,
                    'notes'            => $validated['notes'] ?? null,
                    'tax_rate'         => $validated['tax_rate'] ?? config('pos.tax_rate', 10),
                    'dispatch_method'  => $validated['dispatch_method'] ?? null,
                    'tracking_id'      => $validated['tracking_id'] ?? null,
                    'discount'         => $validated['discount'] ?? 0,
                    'delivery_charges' => $validated['delivery_charges'] ?? 0,
                    'weight'           => 0, // temporary, update later after calculation
                ]);

                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    // 🔑 Get correct price based on customer type
                    if (method_exists($product, 'getPriceForCustomerType')) {
                        $unitPrice = $product->getPriceForCustomerType($customerType);
                    } else {
                        $unitPrice = $product->price;
                    }

                    OrderItem::create([
                        'order_id'    => $order->id,
                        'product_id'  => $product->id,
                        'variant_id'  => $item['variant_id'] ?? null,
                        'quantity'    => $item['quantity'],
                        'unit_price'  => $unitPrice,
                        'total_price' => $unitPrice * $item['quantity']
                    ]);

                    // ✅ calculate weight
                    if (!empty($product->weight)) {
                        $totalWeight += $product->weight * $item['quantity'];
                    }

                    if ($product->stock_quantity < $item['quantity']) {
                        throw new \Exception("Not enough stock for {$product->name}");
                    }

                    if ($product->track_inventory) {
                        $product->decrement('stock_quantity', $item['quantity']);

                        $product->inventoryLogs()->create([
                            'action'          => 'order_sale',
                            'quantity_change' => -$item['quantity'],
                            'notes'           => 'Stock reduced for Order #' . $order->order_number,
                            'user_id'         => auth()->id()
                        ]);
                    }
                }

                // ✅ update total weight after looping products
                $order->update([
                    'weight' => $totalWeight
                ]);

                $order->load('items');
                $order->calculateTotals()->save(); // applies tax, discount & delivery charges

                return response()->json([
                    'success'      => true,
                    'order_id'     => $order->id,
                    'receipt_url'  => route('admin.pos.receipt', $order),
                    'order_number' => $order->order_number,
                    'total'        => $order->total,
                    'weight'       => $order->weight // ✅ return weight in response too
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }


    public function downloadReceiptPdf(Order $order)
    {
        $pdf = Pdf::loadView('admin.pos.receipt-pdf', compact('order')); // create a new receipt-pdf.blade.php for PDF layout
        return $pdf->download("Receipt-{$order->order_number}.pdf");
    }

    public function downloadReceipt(Order $order)
    {
        $pdf = Pdf::loadView('admin.pos.receipt', compact('order'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('receipt-'.$order->order_number.'.pdf');
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
            'amount' => 'required|numeric|min:0.01|max:'.$order->total,
            'reason' => 'required|string|max:500'
        ]);

        DB::transaction(function () use ($request, $order) {
            Refund::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'reason' => $request->reason,
                'status' => 'completed'
            ]);

            $order->update([
                'status' => $request->amount >= $order->total ?
                    Order::STATUS_REFUNDED : Order::STATUS_COMPLETED
            ]);

            if ($request->has('return_to_inventory')) {
                foreach ($order->items as $item) {
                    if ($item->product->track_inventory) {
                        $item->product->increment('stock_quantity', $item->quantity);

                        $item->product->inventoryLogs()->create([
                            'action' => 'refund_return',
                            'quantity_change' => $item->quantity,
                            'notes' => 'Restocked due to refund of Order #' . $order->order_number,
                            'user_id' => auth()->id()
                        ]);
                    }
                }
            }
        });

        return back()->with('success', 'Refund processed successfully');
    }
}
