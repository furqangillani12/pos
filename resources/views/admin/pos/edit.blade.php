@extends('layouts.admin')
@section('title', 'Edit Order #' . $order->order_number)

@push('styles')
    <style>
        .edit-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .edit-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .08);
            padding: 20px;
            margin-bottom: 16px;
        }

        .edit-card h3 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #1e293b;
        }

        .item-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
            flex-wrap: wrap;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-input {
            padding: 6px 10px;
            border: 1.5px solid #e5e7eb;
            border-radius: 6px;
            font-size: 13px;
            box-sizing: border-box;
        }

        .item-input:focus {
            border-color: #3b82f6;
            outline: none;
        }

        .btn-add {
            background: #3b82f6;
            color: #fff;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        .btn-add:hover {
            background: #2563eb;
        }

        .btn-remove {
            background: #ef4444;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 4px 10px;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-save {
            background: #16a34a;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .btn-save:hover {
            background: #15803d;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 14px;
        }

        .summary-row.total {
            font-weight: 800;
            font-size: 16px;
            border-top: 2px solid #e5e7eb;
            padding-top: 8px;
            margin-top: 4px;
        }

        .order-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        @media (prefers-color-scheme: dark) {
            .edit-card {
                background: #1f2937;
                box-shadow: 0 1px 6px rgba(0, 0, 0, .3);
            }

            .edit-card h3 {
                color: #f3f4f6;
            }

            .item-row {
                border-color: #374151;
            }

            .item-input {
                background: #374151;
                color: #f3f4f6;
                border-color: #4b5563;
            }

            .summary-row {
                color: #e5e7eb;
            }

            .summary-row.total {
                border-color: #4b5563;
            }
        }

        @media (max-width: 768px) {
            .edit-container {
                padding: 8px;
            }

            .edit-card {
                padding: 12px;
            }

            .order-details-grid {
                grid-template-columns: 1fr;
            }

            .item-row {
                gap: 6px;
            }

            .item-row .edit-product-select {
                width: 100% !important;
                min-width: 0 !important;
                flex: none !important;
            }

            .item-row .edit-qty {
                width: 70px !important;
            }

            .item-row .edit-price {
                width: 80px !important;
            }

            .item-row .edit-item-total {
                min-width: 60px !important;
                font-size: 12px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="edit-container">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h2 style="font-size:20px;font-weight:800;">Edit Order #{{ $order->order_number }}</h2>
            <a href="{{ route('admin.pos.receipt', $order) }}" style="color:#3b82f6;font-size:13px;">View Receipt</a>
        </div>

        <div class="edit-card">
            <h3>Customer</h3>
            <select id="editCustomer" class="item-input" style="width:100%;">
                <option value="">Walk-in Customer</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" data-type="{{ $customer->customer_type }}"
                        {{ $order->customer_id == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }} ({{ $customer->customer_type }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="edit-card">
            <h3>Items</h3>
            <div id="editItems">
                @foreach ($order->items as $item)
                    <div class="item-row" data-product-id="{{ $item->product_id }}">
                        <select class="item-input edit-product-select" style="flex:1;min-width:150px;">
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}" data-price="{{ $p->sale_price }}"
                                    {{ $item->product_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="number" class="item-input edit-qty" value="{{ $item->quantity }}" step="0.01"
                            min="0.01" style="width:80px;" placeholder="Qty">
                        <input type="number" class="item-input edit-price" value="{{ $item->unit_price }}" step="0.01"
                            min="0" style="width:100px;" placeholder="Price">
                        <span class="edit-item-total" style="font-weight:700;min-width:80px;text-align:right;">Rs.
                            {{ number_format($item->total_price, 2) }}</span>
                        <button class="btn-remove" onclick="this.closest('.item-row').remove();recalcEdit();">X</button>
                    </div>
                @endforeach
            </div>
            <button class="btn-add" style="margin-top:10px;" onclick="addEditItem()">+ Add Item</button>
        </div>

        <div class="edit-card">
            <h3>Order Details</h3>
            <div class="order-details-grid">
                <div>
                    <label style="font-size:12px;color:#6b7280;">Tax Rate (%)</label>
                    <input type="number" id="editTaxRate" class="item-input" value="{{ $order->tax_rate }}" step="0.01"
                        min="0" style="width:100%;" oninput="recalcEdit()">
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;">Discount (Rs.)</label>
                    <input type="number" id="editDiscount" class="item-input" value="{{ $order->discount }}"
                        step="0.01" min="0" style="width:100%;" oninput="recalcEdit()">
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;">Delivery Charges</label>
                    <input type="number" id="editDelivery" class="item-input" value="{{ $order->delivery_charges }}"
                        step="0.01" min="0" style="width:100%;" oninput="recalcEdit()">
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;">Amount Paid</label>
                    <input type="number" id="editPaid" class="item-input" value="{{ $order->paid_amount }}"
                        step="0.01" min="0" style="width:100%;">
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;">Payment Method</label>
                    <select id="editPaymentMethod" class="item-input" style="width:100%;">
                        @foreach (['cash', 'jazzcash', 'easypaisa', 'bank', 'cod', 'pending'] as $m)
                            <option value="{{ $m }}" {{ $order->payment_method == $m ? 'selected' : '' }}>
                                {{ ucfirst($m) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;">Dispatch Method</label>
                    <select id="editDispatch" class="item-input" style="width:100%;" onchange="toggleEditTracking()">
                        @foreach (['Self Pickup', 'By Bus', 'TCS', 'Pak Post', 'PostEx'] as $d)
                            <option value="{{ $d }}" {{ $order->dispatch_method == $d ? 'selected' : '' }}>
                                {{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="editTrackingWrap"
                    style="{{ in_array($order->dispatch_method, ['TCS', 'Pak Post', 'PostEx']) ? '' : 'display:none;' }}">
                    <label style="font-size:12px;color:#6b7280;">Tracking ID</label>
                    <input type="text" id="editTrackingId" class="item-input" value="{{ $order->tracking_id }}"
                        style="width:100%;" placeholder="Tracking ID">
                </div>
            </div>
            <div style="margin-top:10px;">
                <label style="font-size:12px;color:#6b7280;">Notes</label>
                <textarea id="editNotes" class="item-input" style="width:100%;min-height:60px;" placeholder="Order notes...">{{ $order->notes }}</textarea>
            </div>
        </div>

        <div class="edit-card">
            <h3>Summary</h3>
            <div id="editSummary">
                <div class="summary-row"><span>Subtotal</span><span id="sumSubtotal">Rs. 0.00</span></div>
                <div class="summary-row"><span>Tax</span><span id="sumTax">Rs. 0.00</span></div>
                <div class="summary-row"><span>Discount</span><span id="sumDiscount">Rs. 0.00</span></div>
                <div class="summary-row"><span>Delivery</span><span id="sumDelivery">Rs. 0.00</span></div>
                <div class="summary-row total"><span>Total</span><span id="sumTotal">Rs. 0.00</span></div>
            </div>
        </div>

        <button class="btn-save" onclick="saveEditOrder()">Save Changes</button>
    </div>
@endsection

@push('scripts')
    <script>
        const productsData = @json($products);
        const fmt = n => parseFloat(n || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

        function addEditItem() {
            const container = document.getElementById('editItems');
            const options = productsData.map(p => `<option value="${p.id}" data-price="${p.sale_price}">${p.name}</option>`)
                .join('');
            const row = document.createElement('div');
            row.className = 'item-row';
            row.innerHTML = `
            <select class="item-input edit-product-select" style="flex:1;min-width:150px;" onchange="onProductChange(this)">${options}</select>
            <input type="number" class="item-input edit-qty" value="1" step="0.01" min="0.01" style="width:80px;" placeholder="Qty" oninput="recalcEdit()">
            <input type="number" class="item-input edit-price" value="${productsData[0]?.sale_price||0}" step="0.01" min="0" style="width:100px;" placeholder="Price" oninput="recalcEdit()">
            <span class="edit-item-total" style="font-weight:700;min-width:80px;text-align:right;">Rs. 0.00</span>
            <button class="btn-remove" onclick="this.closest('.item-row').remove();recalcEdit();">X</button>
        `;
            container.appendChild(row);
            recalcEdit();
        }

        function onProductChange(sel) {
            const opt = sel.options[sel.selectedIndex];
            const priceInput = sel.closest('.item-row').querySelector('.edit-price');
            priceInput.value = opt.dataset.price || 0;
            recalcEdit();
        }

        document.querySelectorAll('.edit-product-select').forEach(s => s.addEventListener('change', function() {
            onProductChange(this);
        }));
        document.querySelectorAll('.edit-qty, .edit-price').forEach(i => i.addEventListener('input', recalcEdit));

        function recalcEdit() {
            let subtotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.edit-qty')?.value || 0);
                const price = parseFloat(row.querySelector('.edit-price')?.value || 0);
                const total = qty * price;
                subtotal += total;
                const totalEl = row.querySelector('.edit-item-total');
                if (totalEl) totalEl.textContent = 'Rs. ' + fmt(total);
            });

            const taxRate = parseFloat(document.getElementById('editTaxRate')?.value || 0);
            const discount = parseFloat(document.getElementById('editDiscount')?.value || 0);
            const delivery = parseFloat(document.getElementById('editDelivery')?.value || 0);
            const afterDiscount = subtotal - discount;
            const tax = afterDiscount * (taxRate / 100);
            const total = afterDiscount + tax + delivery;

            document.getElementById('sumSubtotal').textContent = 'Rs. ' + fmt(subtotal);
            document.getElementById('sumTax').textContent = 'Rs. ' + fmt(tax);
            document.getElementById('sumDiscount').textContent = 'Rs. ' + fmt(discount);
            document.getElementById('sumDelivery').textContent = 'Rs. ' + fmt(delivery);
            document.getElementById('sumTotal').textContent = 'Rs. ' + fmt(total);
        }

        async function saveEditOrder() {
            const items = [];
            document.querySelectorAll('.item-row').forEach(row => {
                items.push({
                    product_id: parseInt(row.querySelector('.edit-product-select').value),
                    quantity: parseFloat(row.querySelector('.edit-qty').value),
                    unit_price: parseFloat(row.querySelector('.edit-price').value),
                });
            });

            if (items.length === 0) {
                alert('Add at least one item.');
                return;
            }

            const data = {
                customer_id: document.getElementById('editCustomer').value || null,
                items: items,
                payment_method: document.getElementById('editPaymentMethod').value,
                paid_amount: parseFloat(document.getElementById('editPaid').value) || 0,
                tax_rate: parseFloat(document.getElementById('editTaxRate').value) || 0,
                discount: parseFloat(document.getElementById('editDiscount').value) || 0,
                delivery_charges: parseFloat(document.getElementById('editDelivery').value) || 0,
                dispatch_method: document.getElementById('editDispatch').value,
                tracking_id: document.getElementById('editTrackingId').value || null,
                notes: document.getElementById('editNotes').value,
            };

            const btn = document.querySelector('.btn-save');
            btn.disabled = true;
            btn.textContent = 'Saving...';

            try {
                const res = await fetch('{{ route('admin.pos.update', $order) }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data),
                });
                const result = await res.json();
                if (result.success) {
                    alert('Order updated successfully!');
                    window.location.href = result.receipt_url;
                } else {
                    throw new Error(result.message || 'Update failed');
                }
            } catch (err) {
                alert('Error: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.textContent = 'Save Changes';
            }
        }

        function toggleEditTracking() {
            const val = document.getElementById('editDispatch').value;
            const wrap = document.getElementById('editTrackingWrap');
            wrap.style.display = (val === 'TCS' || val === 'Pak Post' || val === 'PostEx') ? '' : 'none';
        }

        recalcEdit();
    </script>
@endpush
