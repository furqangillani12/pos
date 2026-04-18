<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\BranchScoped;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    use BranchScoped;

    public function index()
    {
        $categories = $this->scopeBranch(Category::query())->withCount('products')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function store(Request $request)
    {
        $branchId = $this->branchId();
        $scopeBranchId = ($branchId && $branchId !== 'all') ? $branchId : null;

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories', 'name')->where(fn ($q) => $q->where('branch_id', $scopeBranchId)),
            ],
            'description' => 'nullable|string'
        ]);

        if ($scopeBranchId) {
            $validated['branch_id'] = $scopeBranchId;
        }

        Category::create($validated);
        return back()->with('success', 'Category created successfully');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories', 'name')
                    ->where(fn ($q) => $q->where('branch_id', $category->branch_id))
                    ->ignore($category->id),
            ],
            'description' => 'nullable|string'
        ]);

        $category->update($validated);
        return back()->with('success', 'Category updated successfully');
    }

    public function destroy(Request $request, Category $category)
    {
        if ($category->products()->exists()) {
            // First attempt without confirmation → show warning
            if (!$request->has('confirm_delete')) {
                return back()->with('error', 'This category has products. Please confirm to delete the category and all related products.');
            }

            // If confirmed → delete related products
            $category->products()->delete();
        }

        // Delete category itself
        $category->delete();

        return back()->with('success', 'Category and related products deleted successfully');
    }


}
