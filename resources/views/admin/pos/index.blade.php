@extends('layouts.admin')

@section('title', 'Point of Sale')

@section('content')
    <div class="flex h-screen bg-gray-100">
        <!-- Product Selection Panel -->
        <div class="w-3/4 p-4 overflow-y-auto">
            <div class="bg-white rounded-lg shadow p-4 mb-4">
                <div class="flex items-center">
                    <input type="text" placeholder="Search products..."
                           class="flex-1 px-4 py-2 border rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           id="productSearch">
                    <button style="display: none;" class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-700">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Product Categories Tabs -->
            <div class="mb-4">
                <div class="flex space-x-2 overflow-x-auto pb-2">
                    <button class="category-tab px-4 py-2 bg-blue-600 text-white rounded-lg whitespace-nowrap"
                            data-category="all">All Products</button>
                    @foreach($categories as $category)
                        <button class="category-tab px-4 py-2 bg-gray-200 rounded-lg whitespace-nowrap hover:bg-gray-300"
                                data-category="{{ $category->id }}">{{ $category->name }}</button>
                    @endforeach
                </div>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4" id="productGrid">
                @foreach($products as $product)
                    <div class="product-item bg-white rounded-lg shadow overflow-hidden cursor-pointer transition-transform hover:scale-105"
                         data-id="{{ $product->id }}"
                         data-name="{{ $product->name }}"
                         data-price="{{ $product->price }}"
                         data-category-id="{{ $product->category_id }}">
                        <div class="h-40 bg-gray-200 flex items-center justify-center">
                            @if($product->image)
                                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-full object-cover">
                            @else
                                <span class="text-gray-400"><i class="fas fa-box-open fa-3x"></i></span>
                            @endif
                        </div>
                        <div class="p-3">
                            <h3 class="font-semibold text-gray-800 truncate">{{ $product->name }}</h3>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-blue-600 font-bold">Rs. {{ number_format($product->price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Order Panel -->
        <div class="w-1/4 bg-white border-l flex flex-col" data-pos-route="{{ route('admin.pos.store') }}">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold text-gray-800">Current Order</h2>
                <div class="flex items-center mt-2">
                    <select class="customer-select w-full p-2 border rounded">
                        <option value="">Walk-in Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    <button class="ml-2 p-2 text-blue-600 hover:text-blue-800">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <!-- Order Items -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="cart-items space-y-2"></div>
                <div class="empty-cart-message text-center text-gray-500 py-8">
                    <i class="fas fa-shopping-cart fa-3x mb-2"></i>
                    <p>Your cart is empty</p>
                    <p class="text-sm">Add products to get started</p>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="border-t p-4 bg-gray-50">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="subtotal">Rs. 0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax (10%):</span>
                        <span class="tax">Rs. 0.00</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between font-bold">
                        <span>Total:</span>
                        <span class="total">Rs. 0.00</span>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Payment Method</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button class="payment-method-btn py-2 border rounded bg-gray-100 hover:bg-gray-200"
                                data-method="cash">Cash</button>
                        <button class="payment-method-btn py-2 border rounded bg-gray-100 hover:bg-gray-200"
                                data-method="card">Card</button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-2">
                    <button class="checkout-btn w-full py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 disabled:opacity-50"
                            disabled>
                        Process Payment
                    </button>
                    <button class="clear-cart-btn w-full py-2 border border-red-500 text-red-500 rounded-lg hover:bg-red-50">
                        Clear Cart
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Add smooth transitions for product items */
        .product-item {
            transition: all 0.3s ease;
            display: block; /* Ensure default is visible */
        }

        /* Style for hidden products */
        .product-item[style*="display: none"] {
            transform: scale(0.9);
            opacity: 0;
            height: 0;
            margin: 0;
            padding: 0;
            overflow: hidden;
            border: none;
        }
    </style>
@endpush

@push('scripts')
    <script>
        window.posConfig = {
            storeRoute: "{{ route('admin.pos.store') }}"
        };
    </script>
    <script src="{{ asset('js/app.js') }}"></script>
@endpush
