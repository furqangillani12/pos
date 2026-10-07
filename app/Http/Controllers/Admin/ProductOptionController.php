<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductColor;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Managed lists for the Size and Colour dropdowns on the product form
 * (like Units). One page, two lists.
 */
class ProductOptionController extends Controller
{
    private const TYPES = ['size' => ProductSize::class, 'color' => ProductColor::class];

    public function index()
    {
        $sizes  = ProductSize::orderBy('sort_order')->orderBy('name')->get();
        $colors = ProductColor::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.product-options.index', compact('sizes', 'colors'));
    }

    public function store(Request $request, string $type)
    {
        $model = $this->model($type);
        $table = (new $model)->getTable();
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100', Rule::unique($table, 'name')],
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $model::create(['name' => trim($data['name']), 'sort_order' => $data['sort_order'] ?? 0, 'is_active' => true]);
        return back()->with('success', ucfirst($type) . " \"{$data['name']}\" added.");
    }

    public function update(Request $request, string $type, int $id)
    {
        $model = $this->model($type);
        $item  = $model::findOrFail($id);
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100', Rule::unique($item->getTable(), 'name')->ignore($item->id)],
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $item->update(['name' => trim($data['name']), 'sort_order' => $data['sort_order'] ?? 0]);
        return back()->with('success', ucfirst($type) . ' updated.');
    }

    public function toggle(string $type, int $id)
    {
        $item = $this->model($type)::findOrFail($id);
        $item->update(['is_active' => !$item->is_active]);
        return back()->with('success', ucfirst($type) . " \"{$item->name}\" " . ($item->is_active ? 'enabled.' : 'disabled.'));
    }

    public function destroy(string $type, int $id)
    {
        $item = $this->model($type)::findOrFail($id);
        $item->delete();
        return back()->with('success', ucfirst($type) . " \"{$item->name}\" deleted. Products already using it keep their value.");
    }

    private function model(string $type): string
    {
        abort_unless(isset(self::TYPES[$type]), 404);
        return self::TYPES[$type];
    }
}
