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

        // Attach branch stock for display — one query for all products (was one per
        // product, ~1,800 queries on live). "All branches" = sum across branches,
        // same as Product::getStockForBranch().
        $branchId = $this->branchId();
        $stockQ = BranchProductStock::query()->whereIn('product_id', $products->pluck('id'));
        if ($branchId && $branchId !== 'all') $stockQ->where('branch_id', $branchId);
        $stock = $stockQ->selectRaw('product_id, SUM(stock_quantity) as qty')->groupBy('product_id')->pluck('qty', 'product_id');
        foreach ($products as $product) {
            $product->branch_stock = (float) ($stock[$product->id] ?? 0);
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
        return view('admin.products.create', $this->formData());
    }

    public function store(Request $request)
    {
        $branchId = $this->branchId();
        $product  = new Product();
        if ($branchId && $branchId !== 'all') $product->branch_id = $branchId;

        if ($error = $this->saveProduct($request, $product)) {
            return back()->withInput()->withErrors(['rows' => $error]);
        }

        $product->inventoryLogs()->create([
            'action'          => 'initial',
            'quantity_change' => (float) $product->stock_quantity,
            'branch_id'       => $branchId !== 'all' ? $branchId : null,
            'notes'           => 'Initial stock entry',
            'user_id'         => auth()->id(),
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        $product->branch_stock = $product->getStockForBranch($this->branchId());
        $product->load('variants', 'categories');
        return view('admin.products.edit', ['product' => $product] + $this->formData());
    }

    public function update(Request $request, Product $product)
    {
        if ($error = $this->saveProduct($request, $product)) {
            return back()->withInput()->withErrors(['rows' => $error]);
        }
        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    /** Dropdown data for the product form. */
    private function formData(): array
    {
        return [
            'categories' => $this->scopeBranch(Category::query())->get(),
            'units'      => Unit::where('is_active', true)->get(),
            'sizes'      => \App\Models\ProductSize::active()->get(),
            'colors'     => \App\Models\ProductColor::active()->get(),
        ];
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

    // ── Product form (client design, Oct 2026) ───────────────────────────────

    private const PRICE_COLUMNS = ['cost' => 'cost_price', 'wholesale' => 'wholesale_price', 'resale' => 'resale_price', 'walkin' => 'sale_price'];

    /**
     * Validate and save the whole product form: details, price grid, product-type
     * rows (plain product or size/colour variants), billed other charges, images
     * and video. Returns an error message for the rows, or null when saved.
     */
    private function saveProduct(Request $request, Product $product): ?string
    {
        $isNew = !$product->exists;
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'name_ur'           => 'nullable|string|max:255',
            'barcode'           => 'nullable|string|unique:products,barcode' . ($isNew ? '' : ',' . $product->id),
            'category_id'       => 'required|exists:categories,id',
            'unit_id'           => 'nullable|exists:units,id',
            'rank'              => 'nullable|string|max:50',
            'note'              => 'nullable|string',
            'note_ur'           => 'nullable|string',
            'description'       => 'nullable|string',
            'description_ur'    => 'nullable|string',
            'categories'        => 'nullable|array',
            'categories.*'      => 'exists:categories,id',
            'pricing'           => 'required|array',
            'pricing.*.*'       => 'nullable|numeric|min:0',
            'rows'              => 'required|array|min:1',
            'rows.*.id'         => 'nullable|integer',
            'rows.*.size'       => 'nullable|string|max:100',
            'rows.*.color'      => 'nullable|string|max:100',
            'rows.*.price'      => 'nullable|numeric|min:0',
            'rows.*.qty'        => 'nullable|numeric|min:0',
            'rows.*.weight'     => 'nullable|numeric|min:0',
            'rows.*.reorder'    => 'nullable|numeric|min:0',
            'rows.*.images'     => 'nullable|array',
            'rows.*.images.*'   => 'image|max:5120',
            'rows.*.remove_images' => 'nullable|array',
            'charges'           => 'nullable|array',
            'charges.*.title'   => 'nullable|string|max:120',
            'charges.*.amount'  => 'nullable|numeric|min:0',
            'images'            => 'nullable|array',
            'images.*'          => 'image|max:5120',
            'main_image'        => 'nullable|string',
            'remove_gallery'    => 'nullable|array',
            'video'             => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime|max:51200',
            'video_url'         => 'nullable|url|max:500',
            'remove_video'      => 'nullable|boolean',
        ]);

        // Product-type rows: one row without size/colour = plain product.
        $rows = collect($data['rows'])->values()->map(fn ($r, $i) => [
            'i'       => $i,
            'id'      => !empty($r['id']) ? (int) $r['id'] : null,
            'size'    => trim((string) ($r['size'] ?? '')) ?: null,
            'color'   => trim((string) ($r['color'] ?? '')) ?: null,
            'price'   => ($r['price'] ?? '') === '' || !isset($r['price']) ? null : (float) $r['price'],
            'qty'     => (float) ($r['qty'] ?? 0),
            'weight'  => ($r['weight'] ?? '') === '' || !isset($r['weight']) ? null : (float) $r['weight'],
            'reorder' => ($r['reorder'] ?? '') === '' || !isset($r['reorder']) ? null : (float) $r['reorder'],
            'remove'  => (array) ($r['remove_images'] ?? []),
        ]);
        $hasVariants = $rows->contains(fn ($r) => $r['size'] || $r['color']);
        if ($hasVariants) {
            if ($rows->contains(fn ($r) => !$r['size'] && !$r['color'])) {
                return 'Each product type row needs a size or a color (or keep a single row with neither for a plain product).';
            }
            $keys = $rows->map(fn ($r) => strtolower($r['size'] . '|' . $r['color']));
            if ($keys->count() !== $keys->unique()->count()) {
                return 'The same size / color combination is listed twice.';
            }
        }

        // Price grid → stored prices. price = walk-in list price (shown struck through on the website).
        [$breakdown, $finals] = $this->priceGrid($data['pricing']);
        $first = $rows->first();

        $product->fill([
            'name'            => $data['name'],
            'name_ur'         => $data['name_ur'] ?? null,
            'barcode'         => $data['barcode'] ?? null,
            'category_id'     => $data['category_id'],
            'unit_id'         => $data['unit_id'] ?? null,
            'rank'            => $data['rank'] ?? null,
            'note'            => $data['note'] ?? null,
            'note_ur'         => $data['note_ur'] ?? null,
            'description'     => $data['description'] ?? null,
            'description_ur'  => $data['description_ur'] ?? null,
            'price_breakdown' => $breakdown,
            'cost_price'      => $finals['cost'],
            'wholesale_price' => $finals['wholesale'] ?: $finals['walkin'],
            'resale_price'    => $finals['resale'] ?: $finals['walkin'],
            'sale_price'      => $finals['walkin'],
            'price'           => max($finals['walkin'], (float) ($breakdown['walkin']['base'] ?? 0)),
            'is_active'       => $request->boolean('is_active'),
            'track_inventory' => $request->boolean('track_inventory'),
            'show_on_website' => $request->boolean('show_on_website'),
            'has_variants'    => $hasVariants,
            'stock_quantity'  => $rows->sum('qty'),
            'reorder_level'   => $first['reorder'] ?? 0,
            'weight'          => $first['weight'],
        ]);

        // Other charges billed to the customer per unit → packing_charge / label used by POS & checkout.
        $charges = collect($data['charges'] ?? [])
            ->map(fn ($c) => ['title' => trim((string) ($c['title'] ?? '')), 'amount' => round((float) ($c['amount'] ?? 0), 2)])
            ->filter(fn ($c) => $c['amount'] > 0)->values();
        $product->other_charges  = $charges->isEmpty() ? null : $charges->all();
        $product->packing_charge = round($charges->sum('amount'), 2);
        $product->packing_label  = $charges->isEmpty() ? null : ($charges->pluck('title')->filter()->implode(', ') ?: 'Other charges');

        // Images: gallery minus removed, plus uploads (and plain-product row images); main image kept or chosen.
        $gallery = collect([$product->image])->merge($product->gallery ?? [])->filter()->unique()->values();
        $remove  = collect($data['remove_gallery'] ?? []);
        foreach ($remove as $rm) if ($gallery->contains($rm)) Storage::disk('public')->delete($rm);
        $gallery = $gallery->reject(fn ($g) => $remove->contains($g))->values();
        foreach ((array) $request->file('images', []) as $f) $gallery->push($f->store('products', 'public'));
        if (!$hasVariants) {
            foreach ((array) $request->file('rows.0.images', []) as $f) $gallery->push($f->store('products', 'public'));
        }
        $main = $data['main_image'] ?? null;
        $main = ($main && $gallery->contains($main)) ? $main : $gallery->first();
        $product->image   = $main;
        $product->gallery = $gallery->reject(fn ($g) => $g === $main)->values()->all() ?: null;

        // Video: upload or link.
        if ($request->boolean('remove_video')) {
            if ($product->video) Storage::disk('public')->delete($product->video);
            $product->video = null;
            $product->video_url = null;
        }
        if ($request->hasFile('video')) {
            if ($product->video) Storage::disk('public')->delete($product->video);
            $product->video = $request->file('video')->store('product-videos', 'public');
        }
        if (array_key_exists('video_url', $data) && !$request->boolean('remove_video')) {
            $product->video_url = $data['video_url'] ?: null;
        }

        $product->save();
        $product->categories()->sync($data['categories'] ?? []);

        if ($hasVariants) {
            $this->saveVariantRows($request, $product, $rows, $breakdown);
        } else {
            // A product that had variants and now has none: switch them off.
            $product->variants()->update(['is_active' => false]);
            $product->update(['color_images' => null]);
        }

        // Branch stock follows the form (sum of rows) at the product's branch.
        $branchId = $this->branchId();
        $stockBranch = ($branchId && $branchId !== 'all') ? $branchId : $product->branch_id;
        if ($stockBranch) {
            BranchProductStock::updateOrCreate(
                ['branch_id' => $stockBranch, 'product_id' => $product->id],
                ['stock_quantity' => $rows->sum('qty'), 'reorder_level' => $first['reorder'] ?? 0]
            );
        }

        return null;
    }

    /**
     * Price − Discount + Charges = Final for each column. Discount / charges come
     * as % and/or Rs; the amount wins when given. Returns [breakdown, finals].
     */
    private function priceGrid(array $pricing): array
    {
        $breakdown = [];
        $finals = [];
        foreach (array_keys(self::PRICE_COLUMNS) as $col) {
            $c    = $pricing[$col] ?? [];
            $n    = fn ($k) => (($c[$k] ?? '') === '' || !isset($c[$k])) ? null : (float) $c[$k];
            $base = $n('base') ?? 0;
            $disc = $n('disc_amt') ?? ($n('disc_pct') !== null ? $base * $n('disc_pct') / 100 : 0);
            $chg  = $n('chg_amt') ?? ($n('chg_pct') !== null ? $base * $n('chg_pct') / 100 : 0);
            $final = round(max(0, $base - $disc + $chg), 2);
            $breakdown[$col] = [
                'base'     => round($base, 2),
                'disc_amt' => $disc ? round($disc, 2) : null,
                'disc_pct' => ($disc && $base) ? round($disc / $base * 100, 2) : null,
                'chg_amt'  => $chg ? round($chg, 2) : null,
                'chg_pct'  => ($chg && $base) ? round($chg / $base * 100, 2) : null,
            ];
            $finals[$col] = $final;
        }
        return [$breakdown, $finals];
    }

    /**
     * Save size / colour rows as variants. A row price is that type's walk-in list
     * price; its walk-in / resale / wholesale prices apply the grid's discount and
     * charge percentages. Rows removed from the form are deleted, or switched off
     * when already used on an order or cart.
     */
    private function saveVariantRows(Request $request, Product $product, $rows, array $breakdown): void
    {
        $pct = fn ($col) => 1 - (float) ($breakdown[$col]['disc_pct'] ?? 0) / 100 + (float) ($breakdown[$col]['chg_pct'] ?? 0) / 100;
        $keep = [];
        foreach ($rows as $order => $r) {
            $variant = ($r['id'] ? $product->variants()->where('id', $r['id'])->first() : null)
                ?: $product->variants()->where('color', $r['color'])->where('size', $r['size'])->first();

            $images = collect($variant?->images ?? []);
            foreach ($r['remove'] as $rm) if ($images->contains($rm)) Storage::disk('public')->delete($rm);
            $images = $images->reject(fn ($x) => in_array($x, $r['remove'], true))->values();
            foreach ((array) $request->file("rows.{$r['i']}.images", []) as $f) $images->push($f->store('products', 'public'));

            $vals = [
                'color'           => $r['color'],
                'size'            => $r['size'],
                'base_price'      => $r['price'],
                'sale_price'      => $r['price'] !== null ? round($r['price'] * $pct('walkin'), 2) : null,
                'resale_price'    => $r['price'] !== null ? round($r['price'] * $pct('resale'), 2) : null,
                'wholesale_price' => $r['price'] !== null ? round($r['price'] * $pct('wholesale'), 2) : null,
                'stock'           => $r['qty'],
                'weight'          => $r['weight'],
                'reorder_level'   => $r['reorder'],
                'images'          => $images->isEmpty() ? null : $images->all(),
                'is_active'       => true,
                'sort_order'      => $order,
            ];
            $variant ? $variant->update($vals) : ($variant = $product->variants()->create($vals));
            $keep[] = $variant->id;
        }

        foreach ($product->variants()->whereNotIn('id', $keep)->get() as $old) {
            $used = \App\Models\OrderItem::where('variant_id', $old->id)->exists()
                || \App\Models\CartItem::where('variant_id', $old->id)->exists();
            $used ? $old->update(['is_active' => false, 'stock' => 0]) : $old->delete();
        }

        // Colour photo for the website / POS picker = first image of that colour.
        $colorImages = [];
        foreach ($product->variants()->where('is_active', true)->get() as $v) {
            if ($v->color && !isset($colorImages[$v->color]) && !empty($v->images)) $colorImages[$v->color] = $v->images[0];
        }
        $product->update(['color_images' => $colorImages ?: null]);
    }
}
