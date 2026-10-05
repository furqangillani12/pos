<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\BranchProductStock;
use App\Traits\BranchScoped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    use BranchScoped;
    public function index()
    {
        $products = $this->scopeBranch(Product::query())
            ->with(['category', 'unit'])
            ->get();

        // Attach branch stock for display
        $branchId = $this->branchId();
        foreach ($products as $product) {
            $product->branch_stock = $product->getStockForBranch($branchId);
        }

        // Sort by product code (barcode) in natural order, but DESCENDING (client):
        // the newest/last product shows at the TOP and the first product (e.g. ASM313)
        // sinks to the bottom — while the rest keep their code-based arrangement.
        // Blank codes stay at the very bottom.
        $products = $products->sort(function ($a, $b) {
            $ca = trim((string) ($a->barcode ?? ''));
            $cb = trim((string) ($b->barcode ?? ''));
            if ($ca === '' && $cb === '') return 0;
            if ($ca === '') return 1;
            if ($cb === '') return -1;
            return strnatcasecmp($cb, $ca);
        })->values();

        return view('admin.products.index', compact('products'));
    }


    public function create()
    {
        $categories = $this->scopeBranch(Category::query())->get();
        $units = Unit::where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'barcode'          => 'nullable|string|unique:products',
            'category_id'      => 'required|exists:categories,id',
            'unit_id'          => 'nullable|exists:units,id',
            'description'      => 'nullable|string',
            'note'             => 'nullable|string',
            'rank'             => 'nullable|string|max:50', 
            'sale_price'       => 'required|numeric|min:0',
            'resale_price'     => 'required|numeric|min:0',
            'wholesale_price'  => 'required|numeric|min:0',
            'cost_price'       => 'required|numeric|min:0',
            'weight_kg'        => 'nullable|numeric|min:0|decimal:0,4',
            'weight_g'         => 'nullable|integer|min:0',
            'packing_charge'   => 'nullable|numeric|min:0',
            'packing_label'    => 'nullable|string|max:120',
            'stock_quantity'   => 'required|numeric|min:0',
            'reorder_level'    => 'required|numeric|min:0',
            'image'            => 'nullable|image|max:5120',
            'gallery'          => 'nullable|array',
            'gallery.*'        => 'image|max:5120',
            'categories'       => 'nullable|array',
            'categories.*'     => 'exists:categories,id',
            'is_active'        => 'boolean',
            'track_inventory'  => 'boolean',
            'show_on_website'  => 'boolean',
        ] + $this->variantRules());

        // Checkbox: present only when ticked, so resolve explicitly.
        $validated['show_on_website'] = $request->boolean('show_on_website');
        // Packing charge is a NOT NULL decimal. A blank field arrives as '' (this app
        // has no ConvertEmptyStringsToNull middleware), and '' would crash the insert
        // with "Incorrect decimal value" — so coerce blank/null to 0 explicitly.
        $pc = $validated['packing_charge'] ?? null;
        $validated['packing_charge'] = ($pc === null || $pc === '') ? 0 : (float) $pc;
        $validated['packing_label']  = trim((string) ($validated['packing_label'] ?? '')) ?: null;

        if (!empty($validated['weight_kg'])) {
            $weight = $validated['weight_kg'];
        } elseif (!empty($validated['weight_g'])) {
            $weight = $validated['weight_g'] / 1000;
        } else {
            $weight = null;
        }
        unset($validated['weight_kg'], $validated['weight_g']);
        $validated['weight'] = $weight;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Gallery (#5): store each uploaded image; saved as a JSON array on the product.
        if ($request->hasFile('gallery')) {
            $gallery = [];
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('products', 'public');
            }
            $validated['gallery'] = $gallery;
        }

        $this->applyVariantStock($request, $validated);

        // Assign to current branch
        $branchId = $this->branchId();
        if ($branchId && $branchId !== 'all') {
            $validated['branch_id'] = $branchId;
        }

        $product = Product::create($validated);

        // Extra categories (#16).
        $product->categories()->sync($request->input('categories', []));

        if ($error = $this->syncVariants($request, $product)) {
            return back()->withInput()->withErrors(['variants' => $error]);
        }

        // Create branch stock entry
        if ($branchId && $branchId !== 'all') {
            BranchProductStock::updateOrCreate(
                ['branch_id' => $branchId, 'product_id' => $product->id],
                ['stock_quantity' => $validated['stock_quantity'], 'reorder_level' => $validated['reorder_level']]
            );
        }

        // Log inventory change
        $product->inventoryLogs()->create([
            'action'          => 'initial',
            'quantity_change' => $validated['stock_quantity'],
            'branch_id'       => $branchId !== 'all' ? $branchId : null,
            'notes'           => 'Initial stock entry',
            'user_id'         => auth()->id(),
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        $categories = $this->scopeBranch(Category::query())->get();
        $units = Unit::where('is_active', true)->get();
        $product->branch_stock = $product->getStockForBranch($this->branchId());
        return view('admin.products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'barcode'          => 'nullable|string|unique:products,barcode,'.$product->id,
            'category_id'      => 'required|exists:categories,id',
            'unit_id'          => 'nullable|exists:units,id', 
            'description'      => 'nullable|string',
            'note'             => 'nullable|string',
            'rank'             => 'nullable|string|max:50', 
            'sale_price'       => 'required|numeric|min:0',
            'resale_price'     => 'required|numeric|min:0',
            'wholesale_price'  => 'required|numeric|min:0',
            'cost_price'       => 'required|numeric|min:0',
            'weight_kg'        => 'nullable|numeric|min:0|decimal:0,4',
            'weight_g'         => 'nullable|integer|min:0',
            'packing_charge'   => 'nullable|numeric|min:0',
            'packing_label'    => 'nullable|string|max:120',
            'stock_quantity'   => 'required|numeric|min:0',
            'reorder_level'    => 'required|numeric|min:0',
            'image'            => 'nullable|image|max:5120',
            'gallery'          => 'nullable|array',
            'gallery.*'        => 'image|max:5120',
            'categories'       => 'nullable|array',
            'categories.*'     => 'exists:categories,id',
            'is_active'        => 'boolean',
            'track_inventory'  => 'boolean',
            'show_on_website'  => 'boolean',
        ] + $this->variantRules());

        // Checkbox: present only when ticked, so resolve explicitly.
        $validated['show_on_website'] = $request->boolean('show_on_website');
        // Packing charge is a NOT NULL decimal. A blank field arrives as '' (this app
        // has no ConvertEmptyStringsToNull middleware), and '' would crash the insert
        // with "Incorrect decimal value" — so coerce blank/null to 0 explicitly.
        $pc = $validated['packing_charge'] ?? null;
        $validated['packing_charge'] = ($pc === null || $pc === '') ? 0 : (float) $pc;
        $validated['packing_label']  = trim((string) ($validated['packing_label'] ?? '')) ?: null;

        if (!empty($validated['weight_kg'])) {
        $weight = $validated['weight_kg'];
        } elseif (!empty($validated['weight_g'])) {
            $weight = $validated['weight_g'] / 1000;
        } else {
            $weight = null;
        }

        unset($validated['weight_kg'], $validated['weight_g']);
        $validated['weight'] = $weight;

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Gallery (#5): keep existing minus any ticked for removal, then append new uploads.
        $gallery = $product->gallery ?? [];
        $remove  = (array) $request->input('remove_gallery', []);
        foreach ($remove as $rm) {
            if (in_array($rm, $gallery, true)) {
                Storage::disk('public')->delete($rm);
            }
        }
        $gallery = array_values(array_diff($gallery, $remove));
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('products', 'public');
            }
        }
        $validated['gallery'] = $gallery ?: null;

        $this->applyVariantStock($request, $validated);

        $product->update($validated);

        // Extra categories (#16).
        $product->categories()->sync($request->input('categories', []));

        if ($error = $this->syncVariants($request, $product)) {
            return back()->withInput()->withErrors(['variants' => $error]);
        }

        // Sync branch stock if editing from a specific branch
        $branchId = $this->branchId();
        if ($branchId && $branchId !== 'all') {
            BranchProductStock::updateOrCreate(
                ['branch_id' => $branchId, 'product_id' => $product->id],
                ['stock_quantity' => $validated['stock_quantity'], 'reorder_level' => $validated['reorder_level']]
            );
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    /** Quick toggle of website visibility from the products list. */
    public function toggleWebsite(Product $product)
    {
        $product->update(['show_on_website' => ! $product->show_on_website]);

        return back()->with('success', $product->show_on_website
            ? "\"{$product->name}\" is now visible on the website."
            : "\"{$product->name}\" is now hidden from the website.");
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return redirect()->back()->with('success', 'Product deleted successfully');
    }

    // Import form
    public function showImportForm()
    {
        return view('admin.products.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Log::info('Starting product import');
            $import = new ProductsImport();
            Excel::import($import, $request->file('file'));

            return back()->with('success', 'Imported '.$import->getRowCount().' products');
        } catch (\Exception $e) {
            Log::error('Import failed: '.$e->getMessage());
            return back()->with('error', 'Import failed: '.$e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new ProductsExport($this->branchId()), 'products_'.now()->format('Ymd_His').'.xlsx');
    }

    // ── Colour × size variants ───────────────────────────────────────────────

    private function variantRules(): array
    {
        return [
            'has_variants'                => 'nullable|boolean',
            'variants'                    => 'nullable|array',
            'variants.*.id'               => 'nullable|integer',
            'variants.*.color'            => 'nullable|string|max:100',
            'variants.*.size'             => 'nullable|string|max:100',
            'variants.*.barcode'          => 'nullable|string|max:100',
            'variants.*.sale_price'       => 'nullable|numeric|min:0',
            'variants.*.resale_price'     => 'nullable|numeric|min:0',
            'variants.*.wholesale_price'  => 'nullable|numeric|min:0',
            'variants.*.stock'            => 'nullable|numeric|min:0',
            'variants.*.is_active'        => 'nullable|boolean',
            'color_image'                 => 'nullable|array',
            'color_image.*'               => 'nullable|image|max:5120',
            'color_image_names'           => 'nullable|array',
            'remove_color_image'          => 'nullable|array',
        ];
    }

    /** With variants, the product's stock is the sum of its variants' stock. */
    private function applyVariantStock(Request $request, array &$validated): void
    {
        $validated['has_variants'] = $request->boolean('has_variants') && !empty($request->input('variants'));
        foreach (['variants', 'color_image', 'color_image_names', 'remove_color_image'] as $k) unset($validated[$k]);

        if ($validated['has_variants']) {
            $validated['stock_quantity'] = collect($request->input('variants', []))->sum(fn ($v) => (float) ($v['stock'] ?? 0));
        }
    }

    /**
     * Save the variant table: update rows by id, add new ones, and remove rows no
     * longer listed (rows already used on an order or cart are switched off
     * instead). Also stores per-colour photos. Returns an error message or null.
     */
    private function syncVariants(Request $request, Product $product): ?string
    {
        if (!$product->has_variants) return null;

        $rows = collect($request->input('variants', []))
            ->map(fn ($v) => [
                'id'              => !empty($v['id']) ? (int) $v['id'] : null,
                'color'           => trim((string) ($v['color'] ?? '')) ?: null,
                'size'            => trim((string) ($v['size'] ?? '')) ?: null,
                'barcode'         => trim((string) ($v['barcode'] ?? '')) ?: null,
                'sale_price'      => ($v['sale_price'] ?? '') === '' ? null : (float) $v['sale_price'],
                'resale_price'    => ($v['resale_price'] ?? '') === '' ? null : (float) $v['resale_price'],
                'wholesale_price' => ($v['wholesale_price'] ?? '') === '' ? null : (float) $v['wholesale_price'],
                'stock'           => (float) ($v['stock'] ?? 0),
                'is_active'       => (bool) ($v['is_active'] ?? true),
            ])
            ->filter(fn ($v) => $v['color'] || $v['size'])
            ->values();

        // Barcodes must not clash with another product or variant.
        foreach ($rows as $v) {
            if (!$v['barcode']) continue;
            $clash = Product::where('barcode', $v['barcode'])->where('id', '!=', $product->id)->exists()
                || \App\Models\ProductVariant::where('barcode', $v['barcode'])->where('id', '!=', $v['id'] ?? 0)->exists()
                || $rows->where('barcode', $v['barcode'])->count() > 1;
            if ($clash) return "Barcode {$v['barcode']} is already used by another product or variant.";
        }

        $keep = [];
        foreach ($rows as $i => $v) {
            $data = collect($v)->except('id')->all() + ['sort_order' => $i];
            $variant = $v['id'] ? $product->variants()->where('id', $v['id'])->first() : null;
            if (!$variant) {
                // Same colour/size typed again after removal → reuse that row.
                $variant = $product->variants()->where('color', $v['color'])->where('size', $v['size'])->first();
            }
            $variant ? $variant->update($data) : ($variant = $product->variants()->create($data));
            $keep[] = $variant->id;
        }

        foreach ($product->variants()->whereNotIn('id', $keep)->get() as $old) {
            $used = \App\Models\OrderItem::where('variant_id', $old->id)->exists()
                || \App\Models\CartItem::where('variant_id', $old->id)->exists();
            $used ? $old->update(['is_active' => false, 'stock' => 0]) : $old->delete();
        }

        // Per-colour photos.
        $images  = $product->color_images ?? [];
        $colors  = $rows->pluck('color')->filter()->unique()->values()->all();
        foreach ((array) $request->input('remove_color_image', []) as $c) {
            if (isset($images[$c])) { Storage::disk('public')->delete($images[$c]); unset($images[$c]); }
        }
        foreach ((array) $request->file('color_image', []) as $k => $file) {
            $color = $request->input("color_image_names.$k");
            if (!$file || !$color) continue;
            if (isset($images[$color])) Storage::disk('public')->delete($images[$color]);
            $images[$color] = $file->store('products', 'public');
        }
        $images = array_intersect_key($images, array_flip($colors));
        $product->update(['color_images' => $images ?: null]);

        $product->refresh()->syncStockFromVariants();
        return null;
    }
}
