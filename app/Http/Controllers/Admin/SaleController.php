<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Traits\BranchScoped;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Sales: X% off chosen products, for chosen customer types, between two dates. */
class SaleController extends Controller
{
    use BranchScoped;

    public function index()
    {
        $sales = $this->scopeBranch(Sale::withCount('products'))->latest()->get();
        return view('admin.sales.index', compact('sales'));
    }

    public function create()
    {
        return view('admin.sales.form', ['sale' => new Sale(['is_active' => true, 'applies_to' => ['customer']])] + $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $branchId = $this->branchId();
        $sale = Sale::create($data + ['branch_id' => $branchId !== 'all' ? $branchId : null]);
        $sale->products()->sync($request->input('product_ids', []));
        \App\Services\SaleService::forget();

        return redirect()->route('admin.sales.index')->with('success', "Sale \"{$sale->name}\" saved.");
    }

    public function edit(Sale $sale)
    {
        $sale->load('products:id');
        return view('admin.sales.form', compact('sale') + $this->formData());
    }

    public function update(Request $request, Sale $sale)
    {
        $sale->update($this->validated($request));
        $sale->products()->sync($request->input('product_ids', []));
        \App\Services\SaleService::forget();

        return redirect()->route('admin.sales.index')->with('success', "Sale \"{$sale->name}\" updated.");
    }

    public function toggle(Sale $sale)
    {
        $sale->update(['is_active' => !$sale->is_active]);
        return back()->with('success', "Sale \"{$sale->name}\" " . ($sale->is_active ? 'switched on.' : 'switched off.'));
    }

    public function destroy(Sale $sale)
    {
        $sale->products()->detach();
        $sale->delete();
        return redirect()->route('admin.sales.index')->with('success', 'Sale deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'             => 'required|string|max:191',
            'discount_percent' => 'required|numeric|min:0.01|max:100',
            'applies_to'       => 'required|array|min:1',
            'applies_to.*'     => [Rule::in(array_keys(Sale::TYPES))],
            'starts_at'        => 'nullable|date',
            'ends_at'          => array_filter(['nullable', 'date', $request->filled('starts_at') ? 'after_or_equal:starts_at' : null]),
            'product_ids'      => 'required|array|min:1',
            'product_ids.*'    => 'integer|exists:products,id',
        ], [
            'applies_to.required'  => 'Tick at least one customer type.',
            'ends_at.after_or_equal' => 'The end date must be after the start date.',
            'product_ids.required' => 'Pick at least one product.',
        ]);

        return [
            'name'             => $data['name'],
            'discount_percent' => (float) $data['discount_percent'],
            'applies_to'       => array_values($data['applies_to']),
            'starts_at'        => $data['starts_at'] ?: null,
            'ends_at'          => $data['ends_at'] ?: null,
            'is_active'        => $request->boolean('is_active'),
        ];
    }

    private function formData(): array
    {
        $products = $this->scopeBranch(Product::query())
            ->orderBy('name')
            ->get(['id', 'name', 'barcode', 'category_id', 'sale_price', 'resale_price', 'wholesale_price', 'image'])
            ->map(fn ($p) => [
                'id'   => $p->id,
                'name' => $p->name,
                'code' => $p->barcode,
                'cat'  => $p->category_id,
                'w'    => (float) $p->sale_price,
                'r'    => (float) ($p->resale_price ?: $p->sale_price),
                'h'    => (float) ($p->wholesale_price ?: $p->sale_price),
            ])->values();

        $categories = $this->scopeBranch(Category::query())->orderBy('name')->get(['id', 'name']);

        return compact('products', 'categories');
    }
}
