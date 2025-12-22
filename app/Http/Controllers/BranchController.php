<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;

class BranchController extends Controller
{
    // Display all branches
    public function index()
    {
        $branches = Branch::latest()->paginate(10);
        return view('admin.branch.index', compact('branches'));
    }

    // Show form to create branch
    public function create()
    {
        return view('admin.branch.create');
    }

    // Store new branch
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:branches,name',
            'location' => 'nullable|string|max:255',
        ]);

        Branch::create($request->only('name', 'location'));

        return redirect()->route('branch.index')->with('success', 'Branch created successfully.');
    }

    // Show edit form
    public function edit(Branch $branch)
    {
        return view('admin.branch.edit', compact('branch'));
    }

    // Update branch
    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:branches,name,' . $branch->id,
            'location' => 'nullable|string|max:255',
        ]);

        $branch->update($request->only('name', 'location'));

        return redirect()->route('branch.index')->with('success', 'Branch updated successfully.');
    }

    // Delete branch
    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('branch.index')->with('success', 'Branch deleted successfully.');
    }

    // Branch selection after login
    public function select()
    {
        $branches = Branch::all();
        return view('admin.branch.select', compact('branches'));
    }

    // Store selected branch in session
    public function storeBranchSelection(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        session(['branch_id' => $request->branch_id]);

        return redirect()->route('admin.dashboard');
    }
}
