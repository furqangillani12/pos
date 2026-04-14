@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Purchase Order — {{ $purchase->invoice_number }}</h1>
            <a href="{{ route('purchases.show', $purchase) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Back to Purchase
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <form action="{{ route('purchases.update', $purchase) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier *</label>
                            <select name="supplier_id" id="supplier_id" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="purchase_date" class="block text-sm font-medium text-gray-700">Purchase Date *</label>
                            <input type="date" name="purchase_date" id="purchase_date" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   value="{{ old('purchase_date', $purchase->purchase_date?->format('Y-m-d') ?? $purchase->purchase_date) }}">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Items *</label>
                        <div id="purchase-items" class="space-y-4">
                            <!-- Existing items will be loaded by JS -->
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
                                <option value="unpaid" {{ $purchase->payment_status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="partial" {{ $purchase->payment_status === 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="paid" {{ $purchase->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <div>
                            <label for="paid_amount" class="block text-sm font-medium text-gray-700">Paid Amount *</label>
                            <input type="number" step="0.01" min="0" name="paid_amount" id="paid_amount" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   value="{{ old('paid_amount', $purchase->paid_amount) }}">
                        </div>
                        <div>
                            <label for="total_amount" class="block text-sm font-medium text-gray-700">Total Amount</label>
                            <input type="text" id="total_amount" readonly
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-100 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   value="{{ number_format($purchase->total_amount, 2) }}">
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('notes', $purchase->notes) }}</textarea>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 text-right">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Update Purchase Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .product-search-wrap { position: relative; }
        .product-search-input {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            box-sizing: border-box;
        }
        .product-search-input:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 1px #3b82f6; }
        .product-dropdown {
            position: absolute; top: 100%; left: 0; right: 0;
            background: #fff; border: 1.5px solid #3b82f6; border-top: none;
            border-radius: 0 0 6px 6px; max-height: 220px; overflow-y: auto;
            z-index: 100; display: none; box-shadow: 0 4px 12px rgba(0,0,0,.12);
        }
        .product-dropdown.show { display: block; }
        .product-option { padding: 8px 10px; font-size: 13px; cursor: pointer; border-bottom: 1px solid #f1f5f9; }
        .product-option:hover { background: #eff6ff; }
        .product-option .po-name { font-weight: 600; color: #1e293b; }
        .product-option .po-meta { font-size: 11px; color: #9ca3af; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const products = @json($products);
            const existingItems = @json($existingItems);
            let itemCount = 0;

            function addItemRow(data = null) {
                itemCount++;
                const itemHtml = `
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 item-row">
            <div class="product-search-wrap">
                <input type="hidden" name="items[${itemCount}][product_id]" class="product-id-hidden" value="${data ? data.product_id : ''}" required>
                <input type="text" class="product-search-input" placeholder="Search by name, code, barcode..." autocomplete="off" value="${data ? data.product_name : ''}">
                <div class="product-dropdown"></div>
            </div>
            <div>
                <input type="number" name="items[${itemCount}][quantity]" required min="1" placeholder="Qty"
                    class="quantity block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    value="${data ? data.quantity : ''}">
            </div>
            <div>
                <input type="number" step="0.01" name="items[${itemCount}][unit_price]" required min="0" placeholder="Unit Price"
                    class="unit-price block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    value="${data ? data.unit_price : ''}">
            </div>
            <div class="flex items-center">
                <span class="total-price text-sm font-medium">${data ? (data.quantity * data.unit_price).toFixed(2) : '0.00'}</span>
                <button type="button" class="ml-auto text-red-500 hover:text-red-700 remove-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>`;
                document.getElementById('purchase-items').insertAdjacentHTML('beforeend', itemHtml);
                addItemEventListeners();
            }

            // Load existing items
            existingItems.forEach(item => addItemRow(item));
            if (existingItems.length === 0) addItemRow();

            document.getElementById('add-item').addEventListener('click', function() {
                addItemRow();
                const rows = document.querySelectorAll('.item-row');
                rows[rows.length - 1].querySelector('.product-search-input').focus();
            });

            function addItemEventListeners() {
                document.querySelectorAll('.remove-item').forEach(btn => {
                    btn.onclick = function() {
                        this.closest('.item-row').remove();
                        calculateTotal();
                    };
                });

                document.querySelectorAll('.product-search-input').forEach(input => {
                    input.onfocus = function() { filterProducts(this); };
                    input.oninput = function() {
                        const hidden = this.closest('.product-search-wrap').querySelector('.product-id-hidden');
                        hidden.value = '';
                        filterProducts(this);
                    };
                });

                document.querySelectorAll('.quantity, .unit-price').forEach(input => {
                    input.oninput = function() { calculateRowTotal(this.closest('.item-row')); };
                });
            }

            function filterProducts(input) {
                document.querySelectorAll('.product-dropdown.show').forEach(d => d.classList.remove('show'));
                const wrap = input.closest('.product-search-wrap');
                const dropdown = wrap.querySelector('.product-dropdown');
                const search = input.value.toLowerCase().trim();

                const filtered = products.filter(p => {
                    const name = (p.name || '').toLowerCase();
                    const barcode = (p.barcode || '').toLowerCase();
                    const catName = (p.category && p.category.name ? p.category.name : '').toLowerCase();
                    return !search || name.includes(search) || barcode.includes(search) || catName.includes(search);
                }).slice(0, 20);

                if (filtered.length === 0) {
                    dropdown.innerHTML = '<div class="product-option" style="color:#9ca3af;cursor:default;">No products found</div>';
                } else {
                    dropdown.innerHTML = filtered.map(p => `
                        <div class="product-option" data-id="${p.id}" data-name="${p.name}" data-price="${p.cost_price || 0}">
                            <div class="po-name">${p.name}</div>
                            <div class="po-meta">${p.barcode || 'No barcode'} · ${p.category ? p.category.name : ''} · Cost: Rs.${parseFloat(p.cost_price||0).toLocaleString()}</div>
                        </div>
                    `).join('');
                }
                dropdown.classList.add('show');

                dropdown.querySelectorAll('.product-option[data-id]').forEach(opt => {
                    opt.onclick = function() {
                        const wrap = this.closest('.product-search-wrap');
                        const row = this.closest('.item-row');
                        wrap.querySelector('.product-search-input').value = this.dataset.name;
                        wrap.querySelector('.product-id-hidden').value = this.dataset.id;
                        row.querySelector('.unit-price').value = this.dataset.price;
                        dropdown.classList.remove('show');
                        calculateRowTotal(row);
                    };
                });
            }

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.product-search-wrap')) {
                    document.querySelectorAll('.product-dropdown.show').forEach(d => d.classList.remove('show'));
                }
            });

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

            calculateTotal();
        });
    </script>
@endsection
