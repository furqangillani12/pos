<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->filter(request(['search']))->paginate(20);
        return view('admin.inventory.index', compact('products'));
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'action' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:1',
            'notes' => 'required|string|max:255'
        ]);

        $product = Product::find($validated['product_id']);

        switch ($validated['action']) {
            case 'add':
                $product->increment('stock_quantity', $validated['quantity']);
                $change = $validated['quantity'];
                break;
            case 'remove':
                $product->decrement('stock_quantity', $validated['quantity']);
                $change = -$validated['quantity'];
                break;
            case 'set':
                $change = $validated['quantity'] - $product->stock_quantity;
                $product->update(['stock_quantity' => $validated['quantity']]);
                break;
        }

        $product->inventoryLogs()->create([
            'action' => $validated['action'],
            'quantity_change' => $change,
            'notes' => $validated['notes'],
            'user_id' => auth()->id()
        ]);

        return back()->with('success', 'Inventory adjusted successfully');
    }

    // app/Http/Controllers/Admin/InventoryController.php

    public function logs()
    {
        $logs = InventoryLog::with(['product', 'user'])
            ->latest()
            ->filter(request()->only(['product_id', 'action']))
            ->paginate(20);

        $products = Product::all();

        return view('admin.inventory.logs', compact('logs', 'products'));
    }

    public function lowStock()
    {
        $products = Product::whereColumn('stock_quantity', '<=', 'reorder_level')->paginate(20);
        return view('admin.inventory.low-stock', compact('products'));
    }
}
