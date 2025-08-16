@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Create Purchase Order</h1>
            <a href="{{ route('purchases.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Back to Purchases
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <form action="{{ route('purchases.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier *</label>
                            <select name="supplier_id" id="supplier_id" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="purchase_date" class="block text-sm font-medium text-gray-700">Purchase Date *</label>
                            <input type="date" name="purchase_date" id="purchase_date" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   value="{{ old('purchase_date', now()->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Items *</label>
                        <div id="purchase-items" class="space-y-4">
                            <!-- Items will be added here by JavaScript -->
                        </div>
                        <button type="button" id="add-item" class="mt-2 bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 rounded text-sm">
                            + Add Item
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700">Payment Status *</label>
                            <select name="payment_status" id="payment_status" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="unpaid">Unpaid</option>
                                <option value="partial">Partial</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                        <div>
                            <label for="paid_amount" class="block text-sm font-medium text-gray-700">Paid Amount *</label>
                            <input type="number" step="0.01" min="0" name="paid_amount" id="paid_amount" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   value="{{ old('paid_amount', 0) }}">
                        </div>
                        <div>
                            <label for="total_amount" class="block text-sm font-medium text-gray-700">Total Amount</label>
                            <input type="text" id="total_amount" readonly
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-100 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   value="0.00">
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 text-right">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Create Purchase Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const products = @json($products);
            let itemCount = 0;

            // Add item row
            document.getElementById('add-item').addEventListener('click', function() {
                itemCount++;
                const itemHtml = `
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 item-row">
            <div>
                <select name="items[${itemCount}][product_id]" required
                    class="product-select block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Select Product</option>
                    ${products.map(p => `<option value="${p.id}" data-price="${p.cost_price}">${p.name} (${p.barcode})</option>`).join('')}
                </select>
            </div>
            <div>
                <input type="number" name="items[${itemCount}][quantity]" required min="1" placeholder="Qty"
                    class="quantity block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div>
                <input type="number" step="0.01" name="items[${itemCount}][unit_price]" required min="0" placeholder="Unit Price"
                    class="unit-price block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div class="flex items-center">
                <span class="total-price text-sm font-medium">0.00</span>
                <button type="button" class="ml-auto text-red-500 hover:text-red-700 remove-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>`;
                document.getElementById('purchase-items').insertAdjacentHTML('beforeend', itemHtml);
                addItemEventListeners();
            });

            // Add first item by default
            document.getElementById('add-item').click();

            function addItemEventListeners() {
                // Remove item
                document.querySelectorAll('.remove-item').forEach(btn => {
                    btn.addEventListener('click', function() {
                        this.closest('.item-row').remove();
                        calculateTotal();
                    });
                });

                // Product select change
                document.querySelectorAll('.product-select').forEach(select => {
                    select.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        const unitPriceInput = this.closest('.item-row').querySelector('.unit-price');
                        if (selectedOption.dataset.price) {
                            unitPriceInput.value = selectedOption.dataset.price;
                            calculateRowTotal(this.closest('.item-row'));
                        }
                    });
                });

                // Quantity/price changes
                document.querySelectorAll('.quantity, .unit-price').forEach(input => {
                    input.addEventListener('input', function() {
                        calculateRowTotal(this.closest('.item-row'));
                    });
                });
            }

            function calculateRowTotal(row) {
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                const total = quantity * unitPrice;
                row.querySelector('.total-price').textContent = total.toFixed(2);
                calculateTotal();
            }

            function calculateTotal() {
                let total = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                    const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                    total += quantity * unitPrice;
                });
                document.getElementById('total_amount').value = total.toFixed(2);
            }
        });
    </script>
@endsection
