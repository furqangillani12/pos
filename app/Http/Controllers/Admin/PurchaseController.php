<?php

// app/Http/Controllers/Admin/PurchaseController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'items.product'])->latest()->paginate(20);
        return view('admin.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::with('category')->get();
        return view('admin.purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'paid_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        // Calculate total amount
        $totalAmount = collect($request->items)->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        $purchase = Purchase::create([
            'supplier_id' => $request->supplier_id,
            'invoice_number' => 'INV-' . Str::upper(Str::random(8)),
            'total_amount' => $totalAmount,
            'paid_amount' => $request->paid_amount,
            'payment_status' => $request->payment_status,
            'purchase_date' => $request->purchase_date,
            'notes' => $request->notes
        ]);

        // Add purchase items
        foreach ($request->items as $item) {
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price']
            ]);

            // Update product stock
            $product = Product::find($item['product_id']);
            $product->increment('stock_quantity', $item['quantity']);
        }

        return redirect()->route('purchases.show', $purchase->id)
            ->with('success', 'Purchase order created successfully');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product']);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function destroy(Purchase $purchase)
    {
        // Restore product quantities before deleting
        foreach ($purchase->items as $item) {
            $product = $item->product;
            $product->decrement('stock_quantity', $item->quantity);
        }

        $purchase->delete();
        return redirect()->route('purchases.index')
            ->with('success', 'Purchase order deleted successfully');
    }
}
