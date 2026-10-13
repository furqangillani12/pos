<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\Product;
use App\Models\Setting;
use App\Traits\BranchScoped;
use Illuminate\Http\Request;

/** Gift products and the "spend Rs X, pick N gifts" rules. */
class GiftController extends Controller
{
    use BranchScoped;

    public function index()
    {
        $gifts = $this->scopeBranch(Gift::with('product'))->orderBy('sort_order')->orderBy('id')->get();
        $products = $this->scopeBranch(Product::query())
            ->where('has_variants', false)
            ->whereNotIn('id', $gifts->pluck('product_id'))
            ->orderBy('name')
            ->get(['id', 'name', 'barcode'])
            ->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'code' => $p->barcode])->values();
        $tiers = Gift::tiers();

        return view('admin.gifts.index', compact('gifts', 'products', 'tiers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'price'      => 'nullable|numeric|min:0',
        ]);
        $product = Product::findOrFail($data['product_id']);
        if ($product->has_variants) {
            return back()->with('error', 'Products with colours / sizes cannot be gifts yet.');
        }

        Gift::firstOrCreate(['product_id' => $product->id], [
            'branch_id'  => $product->branch_id,
            'price'      => (float) ($data['price'] ?? 0),
            'is_active'  => true,
            'sort_order' => (int) Gift::max('sort_order') + 1,
        ]);

        return back()->with('success', "{$product->name} added as a gift.");
    }

    public function update(Request $request, Gift $gift)
    {
        $data = $request->validate(['price' => 'nullable|numeric|min:0']);
        $gift->update([
            'price'     => (float) ($data['price'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);
        return back()->with('success', 'Gift updated.');
    }

    public function destroy(Gift $gift)
    {
        $gift->delete();
        return back()->with('success', 'Gift removed.');
    }

    public function saveTiers(Request $request)
    {
        $request->validate([
            'tiers'         => 'nullable|array',
            'tiers.*.min'   => 'nullable|numeric|min:0',
            'tiers.*.gifts' => 'nullable|integer|min:0|max:50',
        ]);
        $tiers = collect($request->input('tiers', []))
            ->filter(fn ($t) => ($t['min'] ?? '') !== '' && (int) ($t['gifts'] ?? 0) > 0)
            ->map(fn ($t) => ['min' => (float) $t['min'], 'gifts' => (int) $t['gifts']])
            ->sortBy('min')->values()->all();
        Setting::put('gift_tiers', json_encode($tiers));

        return back()->with('success', 'Gift rules saved.');
    }

    /** Gifts in stock at the POS branch, for the "Add gift" button. */
    public function api()
    {
        $branchId = $this->branchId();
        return response()->json(
            $this->scopeBranch(Gift::with('product.unit'))->where('is_active', true)->orderBy('sort_order')->get()
                ->filter(fn ($g) => $g->product && !$g->product->has_variants)
                ->map(fn ($g) => [
                    'id'         => $g->id,
                    'product_id' => $g->product_id,
                    'name'       => $g->product->name,
                    'price'      => (float) $g->price,
                    'weight'     => (float) ($g->product->weight ?? 0),
                    'unit'       => $g->product->unit->abbreviation ?? '',
                    'stock'      => (float) $g->product->getStockForBranch($branchId !== 'all' ? $branchId : $g->product->branch_id),
                    'image'      => $g->product->image ? asset('storage/' . $g->product->image) : null,
                ])->values()
        );
    }
}
