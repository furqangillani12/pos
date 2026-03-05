@extends('layouts.admin')

@section('title', 'Point of Sale')

@push('styles')
    <style>
        body {
            overflow: hidden !important;
        }

        .pos-root {
            display: flex;
            height: calc(100vh - 64px);
            max-height: calc(100vh - 64px);
            background: #f0f4f8;
            overflow: hidden;
        }

        /* ── LEFT: Product Panel ───────────────────────────────────── */
        .pos-products {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 12px 12px 50px 12px;
            gap: 10px;
        }

        /* Sticky toolbar at top of products */
        .pos-toolbar {
            flex-shrink: 0;
            background: #fff;
            border-radius: 10px;
            padding: 10px 12px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pos-toolbar .search-wrap {
            position: relative;
            flex: 1;
        }

        .pos-toolbar .search-wrap svg {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            color: #9ca3af;
            pointer-events: none;
        }

        .pos-toolbar input {
            width: 100%;
            padding: 8px 12px 8px 34px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            font-size: 13.5px;
            background: #f9fafb;
            outline: none;
            transition: border-color .15s;
        }

        .pos-toolbar input:focus {
            border-color: #3b82f6;
            background: #fff;
        }

        /* Category tabs */
        .pos-cats {
            flex-shrink: 0;
            display: flex;
            gap: 7px;
            overflow-x: auto;
            scrollbar-width: none;
            padding-bottom: 2px;
        }

        .pos-cats::-webkit-scrollbar {
            display: none;
        }

        .cat-tab {
            flex-shrink: 0;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 600;
            border: 1.5px solid #e5e7eb;
            background: #fff;
            color: #6b7280;
            cursor: pointer;
            transition: all .15s;
        }

        .cat-tab:hover {
            border-color: #3b82f6;
            color: #2563eb;
        }

        .cat-tab.active {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        /* THE KEY FIX: product grid area is flex:1 with overflow-y:auto */
        .pos-grid-wrap {
            flex: 1;
            /* take all remaining height */
            min-height: 0;
            /* CRITICAL — allows flex child to shrink */
            overflow-y: auto;
            overflow-x: hidden;
            border-radius: 10px;
        }

        .pos-grid-wrap::-webkit-scrollbar {
            width: 5px;
        }

        .pos-grid-wrap::-webkit-scrollbar-track {
            background: transparent;
        }

        .pos-grid-wrap::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .pos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(145px, 1fr));
            gap: 10px;
            padding: 2px 2px 8px;
        }

        /* Product cards */
        .product-item {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
            transition: all .15s ease;
        }

        .product-item:hover {
            border-color: #3b82f6;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, .15);
        }

        .product-item:active {
            transform: scale(.98);
        }

        .product-item .img-area {
            height: 110px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-item .img-area img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-item .img-area i {
            font-size: 2rem;
            color: #d1d5db;
        }

        .product-item .card-info {
            padding: 8px 10px 10px;
        }

        .product-item h3 {
            font-size: 12.5px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.3;
            margin-bottom: 4px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-item .price-text {
            font-size: 13px;
            font-weight: 800;
            color: #2563eb;
        }

        .product-item .barcode-text {
            font-size: 10.5px;
            color: #9ca3af;
            margin-top: 2px;
        }

        .product-item .stock-text {
            font-size: 10.5px;
            color: #9ca3af;
            margin-top: 1px;
        }

        .product-item .stock-low {
            color: #ef4444;
        }

        /* ── RIGHT: Cart Panel ─────────────────────────────────────── */
        .pos-cart {
            width: 320px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            background: #fff;
            border-left: 1px solid #e5e7eb;
        }

        /* Row 1: Cart Header - Fixed */
        .cart-head {
            flex-shrink: 0;
            background: #fff;
            color: #1e293b;
            padding: 12px 14px;
            border-bottom: 1px solid #e5e7eb;
        }

        .cart-head h2 {
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: .3px;
            color: #1e293b;
        }

        .customer-wrap {
            position: relative;
        }

        /* Row 2: Cart Items - Takes remaining space, scrollable */
        .cart-items-wrap {
            flex: 1 1 auto;
            /* This is the KEY - it will grow and shrink */
            min-height: 0;
            /* CRITICAL - allows container to shrink */
            overflow-y: auto;
            padding: 10px 12px;
            background: #f8fafc;
        }

        .cart-items-wrap::-webkit-scrollbar {
            width: 3px;
        }

        .cart-items-wrap::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 3px;
        }

        .empty-cart-message {
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #cbd5e1;
            gap: 8px;
            padding: 20px;
        }

        .empty-cart-message i {
            font-size: 2.5rem;
        }

        .empty-cart-message p {
            font-size: 13px;
        }

        .cart-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            padding: 8px;
            margin-bottom: 6px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
        }

        .cart-item-meta {
            flex: 1;
            min-width: 0;
        }

        .cart-item-meta .name {
            font-size: 12.5px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.3;
        }

        .cart-item-meta .unit {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .cart-item-total {
            font-size: 12.5px;
            font-weight: 800;
            color: #1e293b;
            white-space: nowrap;
        }

        .qty-ctrl {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .qty-btn {
            width: 22px;
            height: 22px;
            border: 1.5px solid #e2e8f0;
            border-radius: 5px;
            background: #fff;
            font-size: 14px;
            font-weight: 800;
            color: #3b82f6;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .1s;
        }

        .qty-btn:hover {
            background: #eff6ff;
            border-color: #3b82f6;
        }

        .qty-num {
            font-size: 12.5px;
            font-weight: 800;
            min-width: 18px;
            text-align: center;
            color: #1e293b;
        }

        .remove-item-btn {
            background: none;
            border: none;
            color: #fca5a5;
            cursor: pointer;
            padding: 2px;
            font-size: 13px;
        }

        .remove-item-btn:hover {
            color: #ef4444;
        }

        /* Row 3: Cart Footer - Fixed height based on content, scrollable if needed */
        .cart-footer {
            flex-shrink: 0;
            /* Don't grow or shrink */
            max-height: 50%;
            /* Limit maximum height */
            overflow-y: auto;
            /* Enable scrolling within footer if content exceeds max-height */
            border-top: 1.5px solid #f1f5f9;
            background: #fff;
            padding-bottom: 50px !important;
        }

        /* Totals */
        .totals-block {
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
        }

        .trow {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12.5px;
            margin-bottom: 5px;
        }

        .trow .lbl {
            color: #6b7280;
        }

        .trow .val {
            font-weight: 700;
            color: #1e293b;
        }

        .trow.grand {
            padding-top: 7px;
            border-top: 2px dashed #e5e7eb;
            margin-top: 4px;
        }

        .trow.grand .lbl {
            font-size: 13.5px;
            font-weight: 800;
            color: #1e293b;
        }

        .trow.grand .val {
            font-size: 16px;
            font-weight: 900;
            color: #2563eb;
        }

        .inline-num {
            width: 60px;
            text-align: right;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 3px 6px;
            font-size: 12px;
            outline: none;
        }

        .inline-num:focus {
            border-color: #3b82f6;
        }

        /* Payment methods grid */
        .pay-section {
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
        }

        .sec-label {
            font-size: 10.5px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 7px;
        }

        .pm-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 5px;
        }

        .pm-btn {
            padding: 6px 4px;
            border: 1.5px solid #e5e7eb;
            border-radius: 7px;
            background: #fff;
            font-size: 11.5px;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            text-align: center;
            transition: all .12s;
        }

        .pm-btn:hover {
            border-color: #3b82f6;
            color: #2563eb;
        }

        .pm-btn.active {
            background: #eff6ff;
            border-color: #3b82f6;
            color: #2563eb;
        }

        /* Partial payment box */
        .payment-box {
            background: #f0f9ff;
            border: 1.5px solid #bae6fd;
            border-radius: 10px;
            padding: 10px 12px;
            margin: 0 14px 10px;
        }

        .payment-box label {
            font-size: 11px;
            font-weight: 800;
            color: #0369a1;
            margin-bottom: 5px;
            display: block;
        }

        .payment-big-input {
            width: 100%;
            border: 2px solid #38bdf8;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 17px;
            font-weight: 900;
            color: #0c4a6e;
            outline: none;
            background: #fff;
        }

        .payment-big-input:focus {
            border-color: #0ea5e9;
        }

        .bal-summary {
            margin-top: 8px;
            font-size: 11.5px;
        }

        .bal-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
        }

        .bal-row.prev {
            color: #c2410c;
        }

        .bal-row.remaining {
            color: #dc2626;
        }

        .bal-row.change {
            color: #16a34a;
        }

        .bal-row.new-bal {
            font-weight: 800;
            border-top: 1px solid #bae6fd;
            padding-top: 5px;
            margin-top: 3px;
        }

        /* Dispatch */
        .dispatch-section {
            padding: 0 14px 10px;
        }

        .pos-select {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 13px;
            color: #374151;
            outline: none;
            background: #fff;
            cursor: pointer;
        }

        .pos-select:focus {
            border-color: #3b82f6;
        }

        .action-section {
            padding: 10px 14px 24px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .btn-process {
            width: 100%;
            padding: 13px 16px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(37, 99, 235, .35);
            transition: all .15s;
            letter-spacing: .2px;
        }

        .btn-process:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, .4);
        }

        .btn-process:active {
            transform: none;
        }

        .btn-process:disabled {
            opacity: .6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-clear {
            width: 100%;
            padding: 9px;
            border: 1.5px solid #fca5a5;
            background: #fff;
            color: #ef4444;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all .15s;
        }

        .btn-clear:hover {
            background: #fef2f2;
            border-color: #ef4444;
        }

        /* Customer search icon button */
        .cust-search-ico {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, .6);
            cursor: pointer;
            font-size: 14px;
            padding: 0;
        }

        /* selected customer info block */
        #selectedCustomerInfo {
            margin-top: 8px;
            padding: 6px 10px;
            background: rgba(255, 255, 255, .12);
            border-radius: 8px;
            font-size: 12px;
            display: none;
        }

        #selectedCustomerInfo .inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-item .stock-low {
            color: #ef4444;
        }

        .product-item .stock-ok {
            color: #16a34a;
        }

        /* ── MOBILE ─────────────────────────────────────────────────── */
        @media (max-width: 767px) {
            .pos-root {
                flex-direction: column;
                height: 100vh;
            }

            .pos-cart {
                width: 100%;
                height: 44vh;
                min-height: 44vh;
                max-height: 44vh;
                border-left: none;
                border-bottom: 2px solid #e5e7eb;
                flex-shrink: 0;
            }

            .cart-footer {
                max-height: 30vh;
            }

            .pos-products {
                height: 56vh;
                flex: none;
                padding: 8px;
            }

            .pos-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .product-item .img-area {
                height: 75px;
            }
        }

        @media (min-width: 1280px) {
            .pos-cart {
                width: 350px;
            }

            .pos-grid {
                grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
            }
        }
    </style>
@endpush

@section('content')
    <div class="pos-root">

        {{-- ══════════════════════════════════════════════════════
         LEFT — PRODUCTS PANEL
    ══════════════════════════════════════════════════════ --}}
        <div class="pos-products">

            {{-- Search toolbar --}}
            <div class="pos-toolbar">
                <div class="search-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd" />
                    </svg>
                    <input type="text" id="productSearch" placeholder="Search products by name or barcode...">
                </div>
            </div>

            {{-- Category tabs --}}
            <div class="pos-cats">
                <button class="cat-tab active" data-category="all">All Products</button>
                @foreach ($categories as $category)
                    <button class="cat-tab" data-category="{{ $category->id }}">{{ $category->name }}</button>
                @endforeach
            </div>

            {{-- ⬇⬇ THIS IS THE SCROLLABLE PRODUCT AREA ⬇⬇ --}}
            <div class="pos-grid-wrap">
                <div class="pos-grid" id="productGrid">
                    @foreach ($products as $product)
                        <div class="product-item" data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                            data-barcode="{{ $product->barcode ?? '' }}" data-price="{{ $product->sale_price }}"
                            data-sale-price="{{ $product->sale_price }}" data-resale-price="{{ $product->resale_price }}"
                            data-wholesale-price="{{ $product->wholesale_price }}" data-weight="{{ $product->weight ?? 0 }}"
                            data-category-id="{{ $product->category_id }}">
                            <div class="img-area">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                @else
                                    <i class="fas fa-box-open"></i>
                                @endif
                            </div>
                            <div class="card-info">
                                <h3>{{ $product->name }}</h3>
                                @if ($product->barcode)
                                    <div class="barcode-text"><i class="fas fa-barcode"></i> {{ $product->barcode }}</div>
                                @endif
                                <div class="price-text">Rs. {{ number_format($product->sale_price, 2) }}</div>
                                {{-- AFTER --}}
                                @if ($product->rank)
                                    <div class="stock-text">Box: {{ $product->rank }}</div>
                                @endif
                                <div
                                    class="stock-text {{ $product->stock_quantity <= ($product->reorder_level ?? 5) ? 'stock-low' : 'stock-ok' }}">
                                    Stock: {{ $product->stock_quantity }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ══════════════════════════════════════════════════════
         RIGHT — CART PANEL (3-row flex column)
         Row 1: Header (fixed)
         Row 2: Cart items (scrollable, flex:1)
         Row 3: Footer totals+payment (capped height, scrollable)
    ══════════════════════════════════════════════════════ --}}
        <div class="pos-cart" data-pos-route="{{ route('admin.pos.store') }}">

            {{-- ── ROW 1: HEADER (fixed) ── --}}
            <div class="cart-head">
                <h2>🛒 Current Order</h2>

                {{-- Customer select with search --}}
                <div class="customer-wrap" style="position:relative;">

                    {{-- AFTER --}}
                    <input type="text" id="customerSearchInput" placeholder="🔍 Search or select customer..."
                        autocomplete="off"
                        style="width:100%;padding:8px 10px;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:8px;color:#1e293b;font-size:13px;outline:none;"
                        onfocus="this.style.borderColor='#3b82f6';this.style.background='#fff'"
                        onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb'">

                    {{-- Hidden actual select (used for form value) --}}
                    <select id="customerSelect" style="display:none;">
                        <option value="">Walk-in Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" data-type="{{ $customer->customer_type }}"
                                data-barcode="{{ $customer->barcode }}" data-name="{{ $customer->name }}"
                                data-phone="{{ $customer->phone }}"
                                data-credit-enabled="{{ $customer->credit_enabled ? '1' : '0' }}"
                                data-credit-limit="{{ $customer->credit_limit }}"
                                data-credit-balance="{{ $customer->current_balance }}"
                                data-credit-available="{{ $customer->available_credit }}">
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Dropdown results --}}
                    <div id="customerResults"
                        style="display:none;position:absolute;left:0;right:0;top:calc(100% + 4px);background:#fff;border:1px solid #e5e7eb;border-radius:8px;max-height:220px;overflow-y:auto;z-index:9999;box-shadow:0 8px 24px rgba(0,0,0,.15);">
                    </div>
                </div>

                {{-- Hidden customer type select --}}
                <select class="customer-type-select" style="display:none;">
                    <option value="walkin" selected>Walk-in</option>
                    <option value="reseller">Reseller</option>
                    <option value="wholesale">Wholesale</option>
                </select>

                {{-- Selected customer info --}}
                <div id="selectedCustomerInfo"
                    style="display:none;margin-top:8px;padding:6px 10px;background:rgba(255,255,255,.12);border-radius:8px;font-size:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <span id="selectedCustomerName" style="font-weight:700;font-size:13px;"></span>
                            <span id="selectedCustomerType" style="font-size:11px;opacity:.7;margin-left:5px;"></span>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span id="customerDueBadge"
                                style="display:none;background:#ef4444;color:#fff;font-size:10px;font-weight:800;padding:2px 7px;border-radius:10px;"></span>
                            <button onclick="clearCustomerSelection()"
                                style="background:none;border:none;color:rgba(0, 0, 0, 0.6);font-size:11px;cursor:pointer;">✕
                                Clear</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── ROW 2: CART ITEMS (scrollable, flex:1) ── --}}
            <div class="cart-items-wrap">
                <div class="cart-items"></div>
                <div class="empty-cart-message">
                    <i class="fas fa-shopping-cart"></i>
                    <p style="font-weight:600;">Cart is empty</p>
                    <p style="font-size:11px;opacity:.7;">Tap products to add</p>
                </div>
            </div>

            {{-- ── ROW 3: FOOTER (totals + payment) ── --}}
            <div class="cart-footer">

                {{-- Totals --}}
                <div class="totals-block">
                    <div class="trow">
                        <span class="lbl">Subtotal</span>
                        <span class="val subtotal">Rs. 0.00</span>
                    </div>
                    <div class="trow">
                        <span class="lbl">
                            Tax
                            <input type="number" id="custom_tax" class="inline-num" value="{{ $tax_rate }}"
                                min="0" step="0.01" oninput="updateCartDisplay();updateBalanceSummary()">
                        </span>
                        <span class="val tax">Rs. 0.00</span>
                    </div>
                    <div class="trow">
                        <span class="lbl">Discount (Rs.)</span>
                        <input type="number" id="discount" class="inline-num" value="0" min="0"
                            step="0.01" oninput="updateCartDisplay();updateBalanceSummary()">

                    </div>
                    <div class="trow">
                        <span class="lbl">Weight</span>
                        <span class="val total-weight" style="color:#94a3b8;font-size:11.5px;">0.00 kg</span>
                    </div>
                    <div class="trow grand">
                        <span class="lbl">Total Bill</span>
                        <span class="val total">Rs. 0.00</span>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="pay-section">
                    <div class="sec-label">ادائیگی کا طریقہ — Payment</div>
                    <div class="pm-grid">
                        <button class="pm-btn active" data-method="cash" onclick="selectPM(this)">💵 Cash</button>
                        <button class="pm-btn" data-method="jazzcash" onclick="selectPM(this)">📱 Jazz</button>
                        <button class="pm-btn" data-method="easypaisa" onclick="selectPM(this)">📱 Easy</button>
                        <button class="pm-btn" data-method="bank" onclick="selectPM(this)">🏦 Bank</button>
                        <button class="pm-btn" data-method="card" onclick="selectPM(this)">💳 Card</button>
                        <button class="pm-btn" data-method="cod" onclick="selectPM(this)">🚚 COD</button>
                    </div>
                    <input type="hidden" id="payment_method" value="cash">
                </div>

                {{-- Partial Payment Box --}}
                <div class="payment-box" id="paymentBalanceBox">
                    {{-- Previous balance --}}
                    <div class="flex justify-between text-sm hidden" id="previousBalanceRow"
                        style="background:#fff7ed;padding:5px 8px;border-radius:6px;margin-bottom:8px;">
                        <span style="color:#c2410c;font-weight:700;">⚠️ پچھلا باقی (Prev. Balance):</span>
                        <span style="color:#c2410c;font-weight:800;" id="previousBalanceDisplay">Rs. 0</span>
                    </div>

                    <label>💵 رقم موصولہ — Amount Received</label>
                    <input type="number" id="paid_amount" name="paid_amount" class="payment-big-input" min="0"
                        step="0.01" placeholder="0.00" oninput="updateBalanceSummary()">

                    {{-- Balance summary rows --}}
                    <div class="bal-summary" id="balanceSummaryRows" style="display:none;">
                        <div class="bal-row" style="color:#6b7280;">
                            <span>Total Bill:</span>
                            <span id="summaryTotalBill" style="font-weight:700;">Rs. 0</span>
                        </div>
                        <div class="bal-row" style="color:#16a34a;">
                            <span>Amount Paid:</span>
                            <span id="summaryAmountPaid" style="font-weight:700;">Rs. 0</span>
                        </div>
                        <div class="bal-row change" id="changeRow" style="display:none;">
                            <span>واپسی (Change):</span>
                            <strong id="changeDisplay">Rs. 0</strong>
                        </div>
                        <div class="bal-row remaining" id="balanceRow" style="display:none;">
                            <span>باقی (Remaining):</span>
                            <strong id="balanceDisplay">Rs. 0</strong>
                        </div>
                        <div class="bal-row new-bal" id="newBalanceRow">
                            <span id="newBalLabel" style="color:#6b7280;">New Account Balance:</span>
                            <strong id="newBalanceDisplay">Rs. 0</strong>
                        </div>
                    </div>
                </div>

                {{-- Dispatch --}}
                <div class="dispatch-section">
                    <div class="sec-label">Dispatch Method</div>
                    <select id="dispatch_method" name="dispatch_method" class="pos-select">
                        <option value="Self Pickup">Self Pickup</option>
                        <option value="By Bus">By Bus</option>
                        <option value="TCS">TCS</option>
                        <option value="Pak Post">Pak Post</option>
                        <option value="TCS-COD">TCS-COD</option>
                        <option value="Pak Post-COD">Pak Post-COD</option>
                    </select>
                    <div id="tracking_id_field" style="display:none;margin-top:6px;">
                        <input type="text" id="tracking_id" class="pos-select" placeholder="Tracking ID"
                            style="margin-bottom:5px;">
                    </div>
                    <div id="delivery_charges_field" style="display:none;margin-top:6px;">
                        <input type="number" id="delivery_charges" class="pos-select"
                            placeholder="Delivery Charges (Rs.)" value="0" min="0" step="0.01"
                            oninput="updateCartDisplay();updateBalanceSummary()">
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="action-section">
                    <button class="btn-process checkout-btn">
                        ✅ Process Payment — رقم وصول کریں
                    </button>
                    <button class="btn-clear clear-cart-btn">
                        🗑 Clear Cart
                    </button>
                </div>

            </div>{{-- end cart-footer --}}
        </div>{{-- end pos-cart --}}

    </div>{{-- end pos-root --}}
@endsection

@push('scripts')
    <script>
        window.posConfig = {
            storeRoute: "{{ route('admin.pos.store') }}"
        };
        window.cart = window.cart || [];

        // ── Format number ──────────────────────────────────────────
        window.formatNumber = function(num) {
            return parseFloat(num || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        };

        // ── Payment method toggle ──────────────────────────────────
        window.selectPM = function(btn) {
            document.querySelectorAll('.pm-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('payment_method').value = btn.dataset.method;
        };

        // ── Dispatch toggle ────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            const dispatchSelect = document.getElementById('dispatch_method');
            if (dispatchSelect) {
                dispatchSelect.addEventListener('change', function() {
                    const val = this.value;
                    const needsTracking = val.includes('TCS') || val.includes('Pak Post');
                    document.getElementById('tracking_id_field').style.display = needsTracking ? 'block' :
                        'none';
                    document.getElementById('delivery_charges_field').style.display = needsTracking ?
                        'block' : 'none';
                });
            }
        });

        // ── Product search + category filter ──────────────────────
        document.getElementById('productSearch')?.addEventListener('input', filterProducts);
        document.querySelectorAll('.cat-tab').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.cat-tab').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                filterProducts();
            });
        });

        function filterProducts() {
            const term = (document.getElementById('productSearch')?.value || '').toLowerCase();
            const catId = document.querySelector('.cat-tab.active')?.dataset.category || 'all';
            document.querySelectorAll('.product-item').forEach(card => {
                const matchSearch = !term ||
                    card.dataset.name.toLowerCase().includes(term) ||
                    (card.dataset.barcode || '').toLowerCase().includes(term);
                const matchCat = catId === 'all' || card.dataset.categoryId === catId;
                card.style.display = (matchSearch && matchCat) ? '' : 'none';
            });
        }

        document.querySelectorAll('.product-item').forEach(card => {
            card.addEventListener('click', function() {
                const typeSelect = document.querySelector('.customer-type-select');
                const type = typeSelect?.value || 'walkin';

                // Read correct price based on current type
                let price = parseFloat(this.dataset.salePrice);
                if (type === 'reseller') price = parseFloat(this.dataset.resalePrice) || price;
                if (type === 'wholesale') price = parseFloat(this.dataset.wholesalePrice) || price;

                const existing = window.cart.find(i => i.id === this.dataset.id);
                if (existing) {
                    existing.quantity++;
                } else {
                    window.cart.push({
                        id: this.dataset.id,
                        name: this.dataset.name,
                        price: price,
                        weight: parseFloat(this.dataset.weight) || 0,
                        quantity: 1,
                        salePrice: parseFloat(this.dataset.salePrice),
                        resalePrice: parseFloat(this.dataset.resalePrice) || parseFloat(this.dataset
                            .salePrice),
                        wholesalePrice: parseFloat(this.dataset.wholesalePrice) || parseFloat(this
                            .dataset.salePrice),
                    });
                }

                this.style.borderColor = '#22c55e';
                setTimeout(() => {
                    this.style.borderColor = '';
                }, 300);
                updateCartDisplay();
            });
        });

        // ── Cart display ───────────────────────────────────────────
        window.updateCartDisplay = function() {
            const cartEl = document.querySelector('.cart-items');
            const emptyEl = document.querySelector('.empty-cart-message');
            const subtotalEl = document.querySelector('.subtotal');
            const totalEl = document.querySelector('.total');
            const taxEl = document.querySelector('.tax');
            const weightEl = document.querySelector('.total-weight');

            if (!cartEl) return;

            if (window.cart.length === 0) {
                cartEl.innerHTML = '';
                emptyEl.style.display = 'flex';
                if (subtotalEl) subtotalEl.textContent = 'Rs. 0.00';
                if (totalEl) totalEl.textContent = 'Rs. 0.00';
                if (taxEl) taxEl.textContent = 'Rs. 0.00';
                if (weightEl) weightEl.textContent = '0.00 kg';
                updateBalanceSummary();
                return;
            }
            emptyEl.style.display = 'none';

            let subtotal = 0,
                totalWeight = 0,
                html = '';
            window.cart.forEach((item, idx) => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                totalWeight += (item.weight || 0) * item.quantity;
                html += `
        <div class="cart-item">
            <div class="cart-item-meta">
                <div class="name">${item.name}</div>
                <div class="unit">Rs. ${formatNumber(item.price)} × ${item.quantity}</div>
            </div>
            <div style="display:flex;align-items:center;gap:5px;">
                <div class="qty-ctrl">
                    <button class="qty-btn" onclick="changeQty(${idx},-1)">−</button>
                    <span class="qty-num">${item.quantity}</span>
                    <button class="qty-btn" onclick="changeQty(${idx},1)">+</button>
                </div>
                <span class="cart-item-total">Rs.${formatNumber(itemTotal)}</span>
                <button class="remove-item-btn" onclick="removeFromCart(${idx})">✕</button>
            </div>
        </div>`;
            });
            cartEl.innerHTML = html;

            const taxRate = parseFloat(document.getElementById('custom_tax')?.value || 0);
            const taxAmt = subtotal * (taxRate / 100);
            const discount = parseFloat(document.getElementById('discount')?.value || 0);
            const delivery = parseFloat(document.getElementById('delivery_charges')?.value || 0);
            const total = subtotal + taxAmt + delivery - discount;

            if (subtotalEl) subtotalEl.textContent = 'Rs. ' + formatNumber(subtotal);
            if (taxEl) taxEl.textContent = 'Rs. ' + formatNumber(taxAmt);
            if (totalEl) totalEl.textContent = 'Rs. ' + formatNumber(total);
            if (weightEl) weightEl.textContent = formatNumber(totalWeight) + ' kg';
            updateBalanceSummary();
        };

        window.changeQty = function(idx, delta) {
            window.cart[idx].quantity += delta;
            if (window.cart[idx].quantity <= 0) window.cart.splice(idx, 1);
            updateCartDisplay();
        };
        window.removeFromCart = function(idx) {
            window.cart.splice(idx, 1);
            updateCartDisplay();
        };
        window.clearCart = function() {
            window.cart = [];
            updateCartDisplay();
        };

        // ── Balance summary ────────────────────────────────────────
        function updateBalanceSummary() {
            const cart = window.cart || [];
            const taxRate = parseFloat(document.getElementById('custom_tax')?.value || 0);
            const discount = parseFloat(document.getElementById('discount')?.value || 0);
            const delivery = parseFloat(document.getElementById('delivery_charges')?.value || 0);
            const subtotal = cart.reduce((s, i) => s + i.price * i.quantity, 0);
            const total = subtotal + subtotal * (taxRate / 100) + delivery - discount;

            let prevBal = 0;
            const csel = document.getElementById('customerSelect');
            if (csel?.value) {
                const opt = csel.options[csel.selectedIndex];
                prevBal = parseFloat(opt?.dataset?.creditBalance || 0);
            }

            const paidRaw = document.getElementById('paid_amount')?.value;
            const paidAmt = (paidRaw !== '' && paidRaw != null) ? parseFloat(paidRaw) : total;
            const balOnOrder = Math.max(0, total - paidAmt);
            const change = Math.max(0, paidAmt - total);
            const newBalance = prevBal + total - paidAmt;

            const summaryRows = document.getElementById('balanceSummaryRows');
            if (summaryRows) summaryRows.style.display = (paidRaw || prevBal > 0) ? 'block' : 'none';

            const set = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.textContent = val;
            };
            const show = (id, v) => {
                const el = document.getElementById(id);
                if (el) el.style.display = v ? '' : 'none';
            };

            set('summaryTotalBill', 'Rs. ' + formatNumber(total));
            set('summaryAmountPaid', 'Rs. ' + formatNumber(paidAmt));

            show('previousBalanceRow', prevBal > 0);
            set('previousBalanceDisplay', 'Rs. ' + formatNumber(prevBal));
            show('changeRow', change > 0);
            set('changeDisplay', 'Rs. ' + formatNumber(change));
            show('balanceRow', balOnOrder > 0);
            set('balanceDisplay', 'Rs. ' + formatNumber(balOnOrder));

            const nbEl = document.getElementById('newBalanceDisplay');
            if (nbEl) {
                if (newBalance > 0) {
                    nbEl.textContent = 'Rs. ' + formatNumber(newBalance);
                    nbEl.style.color = '#dc2626';
                } else if (newBalance < 0) {
                    nbEl.textContent = 'Rs. ' + formatNumber(Math.abs(newBalance)) + ' (پیشگی)';
                    nbEl.style.color = '#16a34a';
                } else {
                    nbEl.textContent = '✅ صاف (Settled)';
                    nbEl.style.color = '#16a34a';
                }
            }
            const lbl = document.getElementById('newBalLabel');
            if (lbl) lbl.textContent = newBalance > 0 ? 'New Balance (Due):' : newBalance < 0 ? 'Advance Credit:' :
                'Account Status:';
        }

        document.addEventListener('DOMContentLoaded', function() {


            const searchInput = document.getElementById('customerSearchInput');
            const customerSelect = document.getElementById('customerSelect');
            const resultsEl = document.getElementById('customerResults');
            const infoBox = document.getElementById('selectedCustomerInfo');
            const nameEl = document.getElementById('selectedCustomerName');
            const typeEl = document.getElementById('selectedCustomerType');
            const dueBadge = document.getElementById('customerDueBadge');

            // Build options list once from the hidden select
            const allOptions = Array.from(customerSelect.querySelectorAll('option')).filter(o => o.value);

            function escapeHtml(s) {
                return (s || '').replace(/[&<>"']/g, m => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                } [m]));
            }

            function showResults(matches) {
                if (!matches.length) {
                    resultsEl.innerHTML =
                        '<div style="padding:10px 12px;color:#9ca3af;font-size:13px;">No customers found</div>';
                } else {
                    resultsEl.innerHTML = matches.slice(0, 50).map(o => {
                        const name = escapeHtml(o.getAttribute('data-name') || o.text);
                        const phone = escapeHtml(o.getAttribute('data-phone') || '');
                        const type = escapeHtml(o.getAttribute('data-type') || '');
                        const bal = parseFloat(o.getAttribute('data-credit-balance') || 0);
                        const balHtml = bal > 0 ?
                            `<span style="color:#ef4444;font-size:10px;font-weight:700;">Due: Rs.${bal.toLocaleString('en-PK')}</span>` :
                            '';
                        return `<div class="customer-result" data-value="${escapeHtml(o.value)}"
                    style="padding:9px 12px;cursor:pointer;border-bottom:1px solid #f8fafc;font-size:13px;color:#1e293b;display:flex;justify-content:space-between;align-items:center;"
                    onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background=''">
                    <div>
                        ${name}
                        <small style="display:block;color:#9ca3af;font-size:11px;">${[type, phone].filter(Boolean).join(' • ')}</small>
                    </div>
                    ${balHtml}
                </div>`;
                    }).join('');
                }
                resultsEl.style.display = 'block';
            }

            // Show all on focus
            searchInput.addEventListener('focus', function() {
                showResults(allOptions);
            });

            // Filter on type
            searchInput.addEventListener('input', function() {
                const term = this.value.trim().toLowerCase();
                if (!term) {
                    showResults(allOptions);
                    return;
                }

                const matches = allOptions.filter(o => [(o.getAttribute('data-name') || ''), (o
                            .getAttribute('data-phone') || ''),
                        (o.getAttribute('data-barcode') || ''), o.textContent
                    ]
                    .some(v => v.toLowerCase().includes(term))
                );
                showResults(matches);
            });

            // Select on click
            resultsEl.addEventListener('mousedown', function(e) {
                const row = e.target.closest('.customer-result[data-value]');
                if (!row) return;
                e.preventDefault(); // prevent input blur before selection registers

                const value = row.dataset.value;
                const opt = allOptions.find(o => o.value === value);
                if (!opt) return;

                customerSelect.value = value;
                searchInput.value = opt.getAttribute('data-name') || opt.text;
                resultsEl.style.display = 'none';

                selectCustomer(opt);
            });

            // Close on blur
            searchInput.addEventListener('blur', function() {
                setTimeout(() => {
                    resultsEl.style.display = 'none';
                }, 150);
            });

            // Keyboard navigation
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    resultsEl.style.display = 'none';
                    this.blur();
                }
                if (e.key === 'Enter') {
                    const first = resultsEl.querySelector('.customer-result[data-value]');
                    if (first) first.dispatchEvent(new MouseEvent('mousedown', {
                        bubbles: true
                    }));
                }
            });

            function selectCustomer(opt) {
                const name = opt.getAttribute('data-name') || opt.text;
                const type = opt.getAttribute('data-type') || '';
                const balance = parseFloat(opt.getAttribute('data-credit-balance') || 0);

                if (nameEl) nameEl.textContent = name;
                if (typeEl) typeEl.textContent = '(' + (type.charAt(0).toUpperCase() + type.slice(1)) + ')';
                infoBox.style.display = 'block';

                if (dueBadge) {
                    dueBadge.style.display = balance > 0 ? 'inline' : 'none';
                    if (balance > 0) dueBadge.textContent = 'Due: Rs. ' + balance.toLocaleString('en-PK');
                }

                // Auto price type
                const typeSelect = document.querySelector('.customer-type-select');
                if (typeSelect) {
                    typeSelect.value = type === 'reseller' ? 'reseller' : type === 'wholesale' ? 'wholesale' :
                        'walkin';
                    typeSelect.dispatchEvent(new Event('change'));
                }

                updateBalanceSummary();
            }
            // ── Update prices when customer type changes ──────────────────
            const customerTypeSelect = document.querySelector('.customer-type-select');
            if (customerTypeSelect) {
                customerTypeSelect.addEventListener('change', function() {
                    const type = this.value;

                    // 1. Update price displayed on every product card
                    document.querySelectorAll('.product-item').forEach(card => {
                        let price = parseFloat(card.dataset.salePrice);
                        if (type === 'reseller') price = parseFloat(card.dataset.resalePrice) ||
                            price;
                        if (type === 'wholesale') price = parseFloat(card.dataset.wholesalePrice) ||
                            price;

                        card.dataset.price = price; // keep data-price in sync
                        const priceEl = card.querySelector('.price-text');
                        if (priceEl) priceEl.textContent = 'Rs. ' + parseFloat(price).toFixed(2);
                    });

                    // 2. Update prices of items already in cart
                    window.cart.forEach(item => {
                        const card = document.querySelector(`.product-item[data-id="${item.id}"]`);
                        if (!card) return;
                        let price = parseFloat(card.dataset.salePrice);
                        if (type === 'reseller') price = parseFloat(card.dataset.resalePrice) ||
                            price;
                        if (type === 'wholesale') price = parseFloat(card.dataset.wholesalePrice) ||
                            price;
                        item.price = price;
                    });

                    // 3. Re-render cart with updated prices
                    updateCartDisplay();
                });
            }
            window.clearCustomerSelection = function() {
                customerSelect.value = '';
                searchInput.value = '';
                infoBox.style.display = 'none';
                if (dueBadge) dueBadge.style.display = 'none';
                updateBalanceSummary();
            };

            // Hook into processOrder to read customerSelect.value (already hidden select)
            customerSelect.addEventListener('change', updateBalanceSummary);

            // Cart buttons
            document.querySelector('.checkout-btn')?.addEventListener('click', async e => {
                e.preventDefault();
                await processOrder();
            });
            document.querySelector('.clear-cart-btn')?.addEventListener('click', () => {
                if (confirm('Clear cart?')) window.clearCart();
            });

            updateCartDisplay();
        });

        // ── Process Order ──────────────────────────────────────────
        async function processOrder() {
            const cart = window.cart || [];
            if (cart.length === 0) {
                alert('Cart is empty. Please add items first.');
                return;
            }

            const paymentMethod = document.getElementById('payment_method')?.value || 'cash';
            const paidRaw = document.getElementById('paid_amount')?.value;
            const paidAmount = (paidRaw !== '' && paidRaw != null) ? parseFloat(paidRaw) : null;
            const discount = parseFloat(document.getElementById('discount')?.value || 0);
            const deliveryCharges = parseFloat(document.getElementById('delivery_charges')?.value || 0);
            const taxRate = parseFloat(document.getElementById('custom_tax')?.value || 0);
            const customerId = document.getElementById('customerSelect')?.value;
            const dispatchMethod = document.getElementById('dispatch_method')?.value || 'Self Pickup';
            const trackingId = document.getElementById('tracking_id')?.value || null;

            const orderData = {
                customer_id: customerId ? parseInt(customerId) : null,
                items: cart.map(i => ({
                    product_id: parseInt(i.id),
                    quantity: parseFloat(i.quantity)
                })),
                payment_method: paymentMethod,
                paid_amount: paidAmount,
                dispatch_method: dispatchMethod,
                tracking_id: trackingId,
                delivery_charges: deliveryCharges,
                tax_rate: taxRate,
                discount: discount,
            };

            const btn = document.querySelector('.checkout-btn');
            btn.disabled = true;
            btn.textContent = 'Processing...';

            try {
                const res = await fetch('/admin/pos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(orderData),
                });
                const data = await res.json();

                if (data.success) {
                    let msg =
                        `✅ Order Processed!\n\nOrder #: ${data.order_number}\nTotal: Rs. ${formatNumber(data.total)}\nPaid: Rs. ${formatNumber(data.paid_amount)}`;
                    if (data.balance_amount > 0) msg += `\nباقی: Rs. ${formatNumber(data.balance_amount)}`;
                    if (data.previous_balance > 0) msg += `\nPrev Balance: Rs. ${formatNumber(data.previous_balance)}`;
                    msg += data.new_balance > 0 ? `\n\n⚠️ Account Due: Rs. ${formatNumber(data.new_balance)}` :
                        data.new_balance < 0 ? `\n\n✅ Advance: Rs. ${formatNumber(Math.abs(data.new_balance))}` :
                        `\n\n✅ Account Settled`;
                    alert(msg);

                    window.clearCart();
                    document.getElementById('paid_amount').value = '';
                    document.getElementById('discount').value = '0';
                    if (document.getElementById('delivery_charges')) document.getElementById('delivery_charges').value =
                        '0';
                    if (document.getElementById('tracking_id')) document.getElementById('tracking_id').value = '';

                    updateBalanceSummary();
                    if (confirm('View receipt?')) window.open(`/admin/pos/receipt/${data.order_id}`, '_blank');

                } else {
                    throw new Error(data.message || 'Order failed');
                }
            } catch (err) {
                alert('❌ Error: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.textContent = '✅ Process Payment — رقم وصول کریں';
            }
        }
    </script>
@endpush
