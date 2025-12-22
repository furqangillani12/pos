@extends('layouts.admin')

<style>
 @media (ms-width: 769px)
  {
    .main-div{
        margin-top:50px;
    }
    
  }
</style>

@section('title', 'Point of Sale')

@section('content')
    <div class="main-div flex flex-col md:flex-row h-[calc(100vh-4rem)] md:h-screen bg-gray-100">
        <!-- Product Selection Panel -->
        <div class="md:w-3/4 p-4 overflow-y-auto order-2 md:order-1">
            <!-- Search Bar -->
            <div class="bg-white rounded-lg shadow p-4 mb-4 sticky top-0 z-10">
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
                         data-barcode="{{ $product->barcode ?? '' }}"
                         data-price="{{ $product->sale_price }}"
                         data-sale-price="{{ $product->sale_price }}"
                         data-resale-price="{{ $product->resale_price }}"
                         data-wholesale-price="{{ $product->wholesale_price }}"
                         data-weight="{{ $product->weight ?? 0 }}"
                         data-category-id="{{ $product->category_id }}">
                        <div class="h-32 md:h-40 bg-gray-200 flex items-center justify-center">
                            @if($product->image)
                                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                            @else
                                <span class="text-gray-400"><i class="fas fa-box-open fa-2x md:fa-3x"></i></span>
                            @endif
                        </div>
                        <div class="p-2 md:p-3">
                            <h3 class="font-semibold text-gray-800 text-sm md:text-base truncate">{{ $product->name }}</h3>
                            <div class="flex justify-between items-center mt-1 md:mt-2">
                                <span class="text-blue-600 font-bold text-sm md:text-base price-text">Rs. {{ number_format($product->sale_price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Order Panel - Show on top for mobile -->
        <div class="md:w-1/4 bg-white border-t md:border-l flex flex-col order-1 md:order-2" data-pos-route="{{ route('admin.pos.store') }}">
            <div class="p-3 md:p-4 border-b">
                <h2 class="text-lg md:text-xl font-bold text-gray-800">Current Order</h2>
                <div class="flex flex-col items-center mt-2 space-y-2">
                    <!-- Row 1: Customer Select -->
                    <div class="w-full">
                        <select class="customer-select w-full p-2 border rounded text-sm md:text-base">
                            <option value="">Walk-in Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->name }} ({{ ucfirst($customer->customer_type) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Row 2: Customer Type Select -->
                    <div class="w-full flex items-center space-x-2">
                        <select class="customer-type-select w-full p-2 border rounded text-sm md:text-base">
                            <option value="walkin" selected>Walk-in Price</option>
                            <option value="reseller">Reseller Price</option>
                            <option value="wholesale">Wholesale Price</option>
                        </select>
                        
                       
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="flex-1 p-3 md:p-4 overflow-y-auto max-h-[40vh] md:max-h-none">
                <div class="cart-items space-y-2"></div>
                <div class="empty-cart-message text-center text-gray-500 py-6 md:py-8">
                    <i class="fas fa-shopping-cart fa-2x md:fa-3x mb-2"></i>
                    <p class="text-sm md:text-base">Your cart is empty</p>
                    <p class="text-xs md:text-sm">Add products to get started</p>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="border-t p-3 md:p-4 bg-gray-50">
                <div class="space-y-1 md:space-y-2 mb-3 md:mb-4">
                    <div class="flex justify-between text-sm md:text-base">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="subtotal font-medium">Rs. 0.00</span>
                    </div>
                    <div class="flex justify-between items-center mb-1 md:mb-2">
                        <label for="custom_tax" class="text-gray-600 text-sm md:text-base">Tax (%):</label>
                        <input type="number" id="custom_tax"
                               class="w-16 md:w-20 p-1 border rounded text-right text-sm md:text-base"
                               value="{{ $tax_rate }}" min="0" step="0.01">
                    </div>
                    <div class="flex justify-between text-sm md:text-base">
                        <span class="text-gray-600">Tax Amount:</span>
                        <span class="tax font-medium">Rs. 0.00</span>
                    </div>

                    <div>
                        <label for="discount" class="block text-xs md:text-sm font-medium text-gray-700">Discount (Amount)</label>
                        <input type="number" id="discount" name="discount" value="0"
                               min="0" step="0.01"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm md:text-base">
                    </div>

                    <div class="border-t pt-1 md:pt-2 flex justify-between font-bold text-sm md:text-base">
                        <span>Total:</span>
                        <span class="total">Rs. 0.00</span>
                    </div>

                    <div class="flex justify-between border-t pt-1 md:pt-2 text-sm md:text-base">
                        <span class="text-gray-600">Total Weight:</span>
                        <span class="total-weight font-medium">0.00 kg</span>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="mb-3 md:mb-4">
                    <label for="payment_method" class="block text-gray-700 mb-1 md:mb-2 text-sm md:text-base">Payment Method</label>
                    <select id="payment_method" name="payment_method"
                            class="w-full border rounded px-2 md:px-3 py-1 md:py-2 focus:outline-none focus:ring focus:border-blue-300 text-sm md:text-base">
                        <option value="cash">Cash</option>
                        <option value="cod">Cash on Delivery (COD)</option>
                        <option value="jazzcash">JazzCash</option>
                        <option value="easypaisa">EasyPaisa</option>
                        <option value="bank">Bank Account</option>
                        <option value="card">Card</option>
                    </select>
                </div>

                <!-- Dispatch Method -->
                <div class="mb-3 md:mb-4">
                    <label class="block text-gray-700 mb-1 md:mb-2 text-sm md:text-base">Dispatch Method</label>
                    <select id="dispatch_method" name="dispatch_method"
                            class="w-full p-1 md:p-2 border rounded text-sm md:text-base">
                        <option value="Self Pickup">Self Pickup</option>
                        <option value="By Bus">By Bus</option>
                        <option value="TCS">TCS</option>
                        <option value="Pak Post">Pak Post</option>
                        <option value="TCS-COD">TCS-COD</option>
                        <option value="Pak Post-COD">Pak Post-COD</option>
                    </select>
                </div>

                <div class="mb-3 md:mb-4 hidden" id="tracking_id_field">
                    <label class="block text-gray-700 mb-1 md:mb-2 text-sm md:text-base">Tracking ID</label>
                    <input type="text" id="tracking_id" name="tracking_id"
                           class="w-full p-1 md:p-2 border rounded text-sm md:text-base"
                           placeholder="Enter Tracking ID">
                </div>

                <div class="mb-3 md:mb-4 hidden" id="delivery_charges_field">
                    <label class="block text-gray-700 mb-1 md:mb-2 text-sm md:text-base">Delivery Charges</label>
                    <input type="number" id="delivery_charges" name="delivery_charges"
                           class="w-full p-1 md:p-2 border rounded text-right text-sm md:text-base"
                           value="0" min="0" step="0.01">
                </div>

                <!-- Actions -->
                <div class="space-y-2">
                    <button class="checkout-btn w-full py-2 md:py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 disabled:opacity-50 text-sm md:text-base"
                            disabled>
                        Process Payment
                    </button>
                    <button class="clear-cart-btn w-full py-1 md:py-2 border border-red-500 text-red-500 rounded-lg hover:bg-red-50 text-sm md:text-base">
                        Clear Cart
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .product-item {
            transition: all 0.3s ease;
            display: block;
        }
        .product-item[style*="display: none"] {
            transform: scale(0.9);
            opacity: 0;
            height: 0;
            margin: 0;
            padding: 0;
            overflow: hidden;
            border: none;
        }
        
        /* Mobile-specific styles */
        @media (max-width: 768px) {
            /* Adjust grid for mobile */
            #productGrid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
            }
            
            /* Ensure cart panel is sticky on mobile */
            .order-1 {
                position: sticky;
                top: 64px;
                z-index: 20;
                max-height: 50vh;
                overflow-y: auto;
            }
            
            /* Product panel scrollable */
            .order-2 {
                height: calc(50vh - 4rem);
                overflow-y: auto;
            }
            
            /* Hide desktop-only elements */
            .desktop-only {
                display: none;
            }
        }
        
        /* Desktop styles remain unchanged */
        @media (min-width: 769px) {
            .mobile-only {
                display: none;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        window.posConfig = {
            storeRoute: "{{ route('admin.pos.store') }}"
        };
        
        // Mobile-specific functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Adjust cart height on mobile
            function adjustMobileLayout() {
                if (window.innerWidth < 768) {
                    const productPanel = document.querySelector('.order-2');
                    const cartPanel = document.querySelector('.order-1');
                    const headerHeight = 64; // Mobile header height
                    
                    if (productPanel && cartPanel) {
                        const viewportHeight = window.innerHeight;
                        const availableHeight = viewportHeight - headerHeight;
                        cartPanel.style.maxHeight = '40vh';
                        productPanel.style.height = 'calc(60vh)';
                    }
                }
            }
            
            // Initial adjustment
            adjustMobileLayout();
            
            // Adjust on resize
            window.addEventListener('resize', adjustMobileLayout);
            
            // Show/hide tracking ID field based on dispatch method
            document.getElementById('dispatch_method').addEventListener('change', function() {
                const trackingField = document.getElementById('tracking_id_field');
                const deliveryChargesField = document.getElementById('delivery_charges_field');
                const dispatchMethod = this.value;
                
                // Show tracking ID for TCS and Pak Post
                if (dispatchMethod.includes('TCS') || dispatchMethod.includes('Pak Post')) {
                    trackingField.classList.remove('hidden');
                    deliveryChargesField.classList.remove('hidden');
                } else {
                    trackingField.classList.add('hidden');
                    deliveryChargesField.classList.add('hidden');
                }
            });
        });
    </script>

    <script src="{{ asset('js/app.js') }}"></script>
@endpush