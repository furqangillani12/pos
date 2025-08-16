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
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class PosController extends Controller
{
    public function index()
    {
        $products = Product::with(['variants', 'category'])->get();

        $customers = Customer::get();
        $categories = Category::all(); // Add this line

        return view('admin.pos.index', [
            'products' => $products,
            'customers' => $customers,
            'categories' => $categories, // Add this line
            'taxRate' => config('pos.tax_rate', 10)
        ]);
    }


    public function storeOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'nullable|exists:customers,id',
                'items' => 'required|array',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'payment_method' => 'required|in:cash,card,mobile_money,mixed',
                'notes' => 'nullable|string'
            ]);


            return DB::transaction(function () use ($validated) {
                // Create the order with default values
                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'customer_id' => $validated['customer_id'],
                    'user_id' => auth()->id(),
                    'order_type' => 'pos',
                    'payment_method' => $validated['payment_method'],
                    'status' => Order::STATUS_COMPLETED,
                    'notes' => isset($validated['notes']) ? $validated['notes'] : null,
                    'tax_rate' => config('pos.tax_rate', 10)
                ]);

                // Add order items
                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'variant_id' => isset($item['variant_id']) ? $item['variant_id'] : null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                        'total_price' => $product->price * $item['quantity']
                    ]);
                    if ($product->stock_quantity < $item['quantity']) {
                        throw new \Exception("Not enough stock for {$product->name}");
                    }

                    if ($product->track_inventory) {

                        $product->decrement('stock_quantity', $item['quantity']);

                        // Log inventory deduction
                        $product->inventoryLogs()->create([
                            'action' => 'order_sale',
                            'quantity_change' => -$item['quantity'],
                            'notes' => 'Stock reduced for Order #' . $order->order_number,
                            'user_id' => auth()->id()
                        ]);
                    }

                }

                // Calculate and save totals
                $order->load('items');
                $order->calculateTotals()->save();

                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'receipt_url' => route('admin.pos.receipt', $order),
                    'order_number' => $order->order_number,
                    'total' => $order->total
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }



    public function downloadReceipt(Order $order)
    {
        $pdf = Pdf::loadView('admin.pos.receipt', compact('order'));

        // Set paper size (optional)
        $pdf->setPaper('a4', 'portrait');

        // Download PDF with a filename
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
            // Create refund record
            $refund = Refund::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'reason' => $request->reason,
                'status' => 'completed'
            ]);

            // Update order status
            $order->update([
                'status' => $request->amount >= $order->total ?
                    Order::STATUS_REFUNDED : Order::STATUS_COMPLETED
            ]);

            // Process inventory returns if needed
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
