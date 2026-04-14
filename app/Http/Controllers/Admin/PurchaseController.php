<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Traits\BranchScoped;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    use BranchScoped;

    public function index()
    {
        $purchases = $this->scopeBranch(Purchase::with(['supplier', 'items.product']))->latest()->paginate(20);
        return view('admin.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = $this->scopeBranch(Supplier::query())->get();
        $products  = $this->scopeBranch(Product::query())->with('category')->get();
        return view('admin.purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'        => 'required|exists:suppliers,id',
            'purchase_date'      => 'required|date',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_status'     => 'required|in:paid,partial,unpaid',
            'paid_amount'        => 'required|numeric|min:0',
            'notes'              => 'nullable|string',
        ]);

        $branchId = $this->branchId();

        $totalAmount = collect($request->items)->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        $purchase = Purchase::create([
            'supplier_id'    => $request->supplier_id,
            'branch_id'      => $branchId !== 'all' ? $branchId : null,
            'invoice_number' => 'INV-' . Str::upper(Str::random(8)),
            'total_amount'   => $totalAmount,
            'paid_amount'    => $request->paid_amount,
            'payment_status' => $request->payment_status,
            'purchase_date'  => $request->purchase_date,
            'notes'          => $request->notes,
        ]);

        foreach ($request->items as $item) {
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id'  => $item['product_id'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
            ]);

            // Update branch stock
            $product = Product::find($item['product_id']);
            if ($branchId && $branchId !== 'all') {
                $product->incrementBranchStock($branchId, $item['quantity']);
            } else {
                $product->increment('stock_quantity', $item['quantity']);
            }
        }

        return redirect()->route('purchases.show', $purchase->id)
            ->with('success', 'Purchase order created successfully');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product']);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $purchase->load(['items.product']);
        $suppliers = $this->scopeBranch(Supplier::query())->get();
        $products  = $this->scopeBranch(Product::query())->with('category')->get();
        $existingItems = $purchase->items->map(function ($item) {
            return [
                'product_id'   => $item->product_id,
                'product_name' => $item->product->name ?? 'Unknown',
                'quantity'     => $item->quantity,
                'unit_price'   => $item->unit_price,
            ];
        });
        return view('admin.purchases.edit', compact('purchase', 'suppliers', 'products', 'existingItems'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'supplier_id'        => 'required|exists:suppliers,id',
            'purchase_date'      => 'required|date',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_status'     => 'required|in:paid,partial,unpaid',
            'paid_amount'        => 'required|numeric|min:0',
            'notes'              => 'nullable|string',
        ]);

        $branchId = $purchase->branch_id;

        // Reverse old stock
        foreach ($purchase->items as $item) {
            $product = $item->product;
            if ($branchId) {
                $product->decrementBranchStock($branchId, $item->quantity);
            } else {
                $product->decrement('stock_quantity', $item->quantity);
            }
        }

        // Delete old items
        $purchase->items()->delete();

        $totalAmount = collect($request->items)->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        $purchase->update([
            'supplier_id'    => $request->supplier_id,
            'total_amount'   => $totalAmount,
            'paid_amount'    => $request->paid_amount,
            'payment_status' => $request->payment_status,
            'purchase_date'  => $request->purchase_date,
            'notes'          => $request->notes,
        ]);

        foreach ($request->items as $item) {
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id'  => $item['product_id'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
            ]);

            // Update branch stock
            $product = Product::find($item['product_id']);
            if ($branchId) {
                $product->incrementBranchStock($branchId, $item['quantity']);
            } else {
                $product->increment('stock_quantity', $item['quantity']);
            }

            // Update product cost price to the new purchase price
            $product->update(['cost_price' => $item['unit_price']]);
        }

        return redirect()->route('purchases.show', $purchase->id)
            ->with('success', 'Purchase order updated successfully');
    }

    public function invoice(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product']);

        // Calculate supplier's previous balance (sum of unpaid amounts from prior purchases)
        $previousPurchases = Purchase::where('supplier_id', $purchase->supplier_id)
            ->where('id', '<', $purchase->id)
            ->get();
        $previousBalance = $previousPurchases->sum(function ($p) {
            return $p->total_amount - $p->paid_amount;
        });

        return view('admin.purchases.invoice', compact('purchase', 'previousBalance'));
    }

    public function destroy(Purchase $purchase)
    {
        $branchId = $purchase->branch_id;

        foreach ($purchase->items as $item) {
            $product = $item->product;
            if ($branchId) {
                $product->decrementBranchStock($branchId, $item->quantity);
            } else {
                $product->decrement('stock_quantity', $item->quantity);
            }
        }

        $purchase->delete();
        return redirect()->route('purchases.index')
            ->with('success', 'Purchase order deleted successfully');
    }
}
