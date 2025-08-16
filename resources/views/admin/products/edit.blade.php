@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Product: {{ $product->name }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Back to Products
                </a>
                <a href="{{ route('products.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Add New Product
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.products._form')
                </form>

                @if($product->variants->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Product Variants</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            @foreach($product->variants as $variant)
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <div>
                                        <span class="font-medium">{{ $variant->variant_name }}:</span>
                                        {{ $variant->variant_value }}
                                        ({{ config('settings.currency_symbol') }}{{ number_format($product->price + $variant->price_adjustment, 2) }})
                                        - Stock: {{ $variant->stock }}
                                    </div>
                                    <form action="{{ route('variants.destroy', $variant->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm" onclick="return confirm('Delete this variant?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Variant</h3>
                    <form action="{{ route('variants.store', $product->id) }}" method="POST" class="bg-gray-50 rounded-lg p-4">
                        @csrf
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                            <div>
                                <label for="variant_name" class="block text-sm font-medium text-gray-700">Variant Type</label>
                                <input type="text" name="variant_name" id="variant_name" required
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="variant_value" class="block text-sm font-medium text-gray-700">Variant Value</label>
                                <input type="text" name="variant_value" id="variant_value" required
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="price_adjustment" class="block text-sm font-medium text-gray-700">Price Adjustment</label>
                                <input type="number" step="0.01" name="price_adjustment" id="price_adjustment" required
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                                <input type="number" min="0" name="stock" id="stock" required
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                Add Variant
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
