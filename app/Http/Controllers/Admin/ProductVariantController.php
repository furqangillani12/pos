<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'variant_name' => 'required|string|max:50',
            'variant_value' => 'required|string|max:50',
            'price_adjustment' => 'required|numeric',
            'stock' => 'required|integer|min:0'
        ]);

        $variant = $product->variants()->create($validated);

        // Log inventory change
        $product->inventoryLogs()->create([
            'action' => 'variant_added',
            'quantity_change' => $validated['stock'],
            'notes' => "Variant {$variant->variant_name}: {$variant->variant_value} added",
            'user_id' => auth()->id()
        ]);

        return back()->with('success', 'Variant added successfully');
    }

    public function destroy(ProductVariant $variant)
    {
        $variant->delete();
        return back()->with('success', 'Variant deleted successfully');
    }
}
