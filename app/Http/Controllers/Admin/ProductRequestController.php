<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductRequest;
use Illuminate\Http\Request;

class ProductRequestController extends Controller
{
    public function index(Request $request)
    {
        $requests = ProductRequest::with('product:id,name,slug', 'customer:id,name')
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.product-requests.index', compact('requests'));
    }

    public function updateStatus(Request $request, ProductRequest $productRequest)
    {
        $data = $request->validate([
            'status' => 'required|in:new,contacted,arranged,closed',
        ]);
        $productRequest->update($data);

        return back()->with('success', 'Request updated.');
    }

    public function destroy(ProductRequest $productRequest)
    {
        $productRequest->delete();

        return back()->with('success', 'Request deleted.');
    }
}
