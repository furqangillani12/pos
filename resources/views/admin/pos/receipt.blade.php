@extends('layouts.admin')

@section('title', 'Receipt #' . $order->order_number)

@push('styles')
<style>
    /* ── Receipt container ── */
    .receipt-wrap {
        max-width: 520px;
        margin: 0 auto;
        padding: 0;
    }

    .receipt-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,.08);
        overflow: hidden;
    }

    /* ── Header ── */
    .receipt-header {
        text-align: center;
        padding: 24px 20px 16px;
        border-bottom: 2px dashed #e5e7eb;
    }

    .receipt-header h1 {
        font-size: 20px;
        font-weight: 800;
        color: #1e293b;
        margin: 0 0 4px;
    }

    .receipt-header p {
        font-size: 13px;
        color: #6b7280;
        margin: 2px 0;
    }

    /* ── Info rows ── */
    .receipt-info {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 3px 0;
        font-size: 13px;
    }

    .info-row .label {
        font-weight: 600;
        color: #374151;
    }

    .info-row .value {
        color: #1e293b;
        text-align: right;
    }

    /* ── Items table ── */
    .receipt-items {
        padding: 0 20px 14px;
        border-bottom: 2px dashed #e5e7eb;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .items-table thead th {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .3px;
        color: #6b7280;
        padding: 10px 0 8px;
        border-bottom: 2px solid #1e293b;
    }

    .items-table thead th:first-child { text-align: left; }
    .items-table thead th:nth-child(2) { text-align: center; width: 50px; }
    .items-table thead th:nth-child(3) { text-align: right; width: 70px; }
    .items-table thead th:last-child { text-align: right; width: 80px; }

    .items-table tbody td {
        padding: 7px 0;
        font-size: 13px;
        color: #1e293b;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: top;
    }

    .items-table tbody td:first-child {
        text-align: left;
        padding-right: 8px;
        word-break: break-word;
    }

    .items-table tbody td:nth-child(2) { text-align: center; font-weight: 500; }
    .items-table tbody td:nth-child(3) { text-align: right; font-family: ui-monospace, monospace; font-size: 12px; }
    .items-table tbody td:last-child { text-align: right; font-family: ui-monospace, monospace; font-size: 12px; font-weight: 600; }

    .items-table tbody tr:nth-child(even) { background: #f9fafb; }

    /* ── Totals ── */
    .receipt-totals {
        padding: 14px 20px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
        font-size: 13px;
    }

    .total-row .label { color: #6b7280; font-weight: 600; }
    .total-row .value { font-family: ui-monospace, monospace; color: #1e293b; font-weight: 600; }

    .total-row.grand {
        border-top: 2px solid #1e293b;
        margin-top: 6px;
        padding-top: 8px;
    }

    .total-row.grand .label { font-size: 15px; font-weight: 800; color: #1e293b; }
    .total-row.grand .value { font-size: 17px; font-weight: 900; color: #1e293b; }

    .total-row.paid { background: #f0fdf4; padding: 6px 8px; border-radius: 6px; margin-top: 6px; }
    .total-row.paid .label { color: #16a34a; }
    .total-row.paid .value { color: #16a34a; }

    .total-row.balance { background: #fef2f2; padding: 6px 8px; border-radius: 6px; margin-top: 4px; }
    .total-row.balance .label { color: #dc2626; }
    .total-row.balance .value { color: #dc2626; }

    .total-row.prev-balance { background: #fff7ed; padding: 5px 8px; border-radius: 6px; margin-top: 4px; }
    .total-row.prev-balance .label { color: #c2410c; font-size: 12px; }
    .total-row.prev-balance .value { color: #c2410c; font-size: 12px; }

    .total-row.due { background: #fefce8; padding: 8px; border-radius: 6px; margin-top: 4px; border: 1.5px solid #fbbf24; }
    .total-row.due .label { color: #a16207; font-weight: 800; }
    .total-row.due .value { color: #a16207; font-weight: 800; }

    .total-row.advance { background: #f0fdf4; padding: 8px; border-radius: 6px; margin-top: 4px; border: 1.5px solid #4ade80; }
    .total-row.advance .label { color: #16a34a; font-weight: 800; }
    .total-row.advance .value { color: #16a34a; font-weight: 800; }

    .total-row.settled { background: #f0fdf4; padding: 8px; border-radius: 6px; margin-top: 4px; border: 1.5px solid #4ade80; text-align: center; }
    .total-row.settled span { color: #16a34a; font-weight: 800; width: 100%; text-align: center; }

    .total-row.discount .label { color: #dc2626; }
    .total-row.discount .value { color: #dc2626; }

    /* ── Payment & Dispatch meta ── */
    .receipt-meta {
        padding: 10px 20px 14px;
        border-top: 1px solid #f1f5f9;
    }

    /* ── Footer ── */
    .receipt-footer {
        text-align: center;
        padding: 16px 20px;
        border-top: 2px dashed #e5e7eb;
        color: #9ca3af;
        font-size: 12px;
    }

    .receipt-footer p { margin: 2px 0; }

    .qr-block {
        margin-top: 12px;
        padding: 10px;
        background: #f9fafb;
        border-radius: 8px;
        display: inline-block;
    }

    .qr-block p { font-size: 10px; color: #9ca3af; }

    #qrcode-container {
        width: 100px;
        height: 100px;
        margin: 6px auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #qrcode-container canvas,
    #qrcode-container img {
        width: 100px !important;
        height: 100px !important;
        max-width: 100px !important;
        max-height: 100px !important;
        display: block !important;
    }

    #qrcode-container canvas + img { display: none !important; }

    .receipt-url {
        font-size: 10px;
        color: #3b82f6;
        word-break: break-all;
        margin-top: 4px;
    }

    /* ── Action buttons ── */
    .receipt-actions {
        max-width: 520px;
        margin: 16px auto 0;
        padding: 0;
    }

    .action-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }

    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 8px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #fff;
        text-decoration: none;
        border: none;
        cursor: pointer;
        white-space: nowrap;
        transition: opacity .15s;
    }

    .action-btn:hover { opacity: .85; }
    .action-btn i { font-size: 14px; }

    .action-btn.view { background: #0891b2; }
    .action-btn.whatsapp { background: #16a34a; }
    .action-btn.print { background: #2563eb; }
    .action-btn.pdf { background: #7c3aed; }
    .action-btn.copy { background: #4f46e5; }
    .action-btn.edit { background: #eab308; color: #1e293b; }
    .action-btn.new-sale { background: #e5e7eb; color: #374151; }

    /* ── Mobile responsive ── */
    @media (max-width: 640px) {
        .receipt-wrap {
            margin: 0;
            max-width: 100%;
        }

        .receipt-card {
            border-radius: 0;
            box-shadow: none;
        }

        .receipt-header {
            padding: 16px 14px 12px;
        }

        .receipt-header h1 { font-size: 17px; }
        .receipt-header p { font-size: 12px; }

        .receipt-info { padding: 10px 14px; }
        .info-row { font-size: 12px; }

        .receipt-items { padding: 0 14px 10px; }

        .items-table thead th { font-size: 10px; padding: 8px 0 6px; }

        .items-table tbody td {
            padding: 6px 0;
            font-size: 12px;
        }

        .items-table tbody td:nth-child(3),
        .items-table tbody td:last-child { font-size: 11px; }

        .receipt-totals { padding: 10px 14px; }
        .total-row { font-size: 12px; }
        .total-row.grand .label { font-size: 14px; }
        .total-row.grand .value { font-size: 15px; }

        .receipt-meta { padding: 8px 14px 10px; }
        .receipt-footer { padding: 12px 14px; }

        .receipt-actions { margin: 10px 0 0; padding: 0 8px; }

        .action-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }

        .action-btn {
            padding: 10px 6px;
            font-size: 11px;
            gap: 4px;
        }

        .action-btn i { font-size: 13px; }
    }

    @media (max-width: 380px) {
        .action-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .items-table thead th:nth-child(3),
        .items-table tbody td:nth-child(3) {
            display: none;
        }

        .items-table thead th:last-child { width: 70px; }
    }

    /* ── Dark mode ── */
    @media (prefers-color-scheme: dark) {
        .receipt-card { background: #1f2937; }
        .receipt-header { border-color: #374151; }
        .receipt-header h1 { color: #f3f4f6; }
        .receipt-header p { color: #9ca3af; }
        .receipt-info { border-color: #374151; }
        .info-row .label { color: #9ca3af; }
        .info-row .value { color: #e5e7eb; }
        .receipt-items { border-color: #374151; }
        .items-table thead th { color: #9ca3af; border-color: #374151; }
        .items-table tbody td { color: #e5e7eb; border-color: #374151; }
        .items-table tbody tr:nth-child(even) { background: #111827; }
        .receipt-totals .total-row .label { color: #9ca3af; }
        .receipt-totals .total-row .value { color: #e5e7eb; }
        .total-row.grand { border-color: #e5e7eb; }
        .total-row.grand .label, .total-row.grand .value { color: #f3f4f6; }
        .total-row.paid { background: #064e3b; }
        .total-row.paid .label, .total-row.paid .value { color: #6ee7b7; }
        .total-row.balance { background: #7f1d1d; }
        .total-row.balance .label, .total-row.balance .value { color: #fca5a5; }
        .total-row.prev-balance { background: #78350f; }
        .total-row.prev-balance .label, .total-row.prev-balance .value { color: #fde68a; }
        .total-row.due { background: #78350f; border-color: #a16207; }
        .total-row.due .label, .total-row.due .value { color: #fde68a; }
        .total-row.advance { background: #064e3b; border-color: #4ade80; }
        .total-row.advance .label, .total-row.advance .value { color: #6ee7b7; }
        .total-row.settled { background: #064e3b; border-color: #4ade80; }
        .total-row.settled span { color: #6ee7b7; }
        .total-row.discount .label, .total-row.discount .value { color: #f87171; }
        .receipt-meta { border-color: #374151; }
        .receipt-footer { border-color: #374151; color: #6b7280; }
        .qr-block { background: #111827; }
        .qr-block p { color: #6b7280; }
        .receipt-url { color: #60a5fa; }
        .action-btn.edit { color: #1e293b; }
        .action-btn.new-sale { background: #374151; color: #d1d5db; }
    }

    /* ── Print styles ── */
    @media print {
        body * { visibility: hidden; }

        .print-content, .print-content * { visibility: visible; }

        .print-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            max-width: 100% !important;
            padding: 15px;
            background: white;
        }

        .receipt-card {
            box-shadow: none;
            border-radius: 0;
        }

        .no-print { display: none !important; }

        #qrcode-container { width: 80px; height: 80px; }
        #qrcode-container canvas, #qrcode-container img {
            width: 80px !important; height: 80px !important;
            max-width: 80px !important; max-height: 80px !important;
        }
    }
</style>
@endpush

@section('content')
    {{-- ── Receipt Card ── --}}
    <div class="receipt-wrap print-content">
        <div class="receipt-card">

            {{-- Header --}}
            <div class="receipt-header">
                <h1>ALMufeed Saqafti Markaz</h1>
                <p>www.almufeed.com.pk</p>
                <p>Phone: 03007951919</p>
            </div>

            {{-- Order Info --}}
            <div class="receipt-info">
                <div class="info-row">
                    <span class="label">Receipt #:</span>
                    <span class="value">{{ $order->order_number }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Date:</span>
                    <span class="value">{{ $order->created_at?->format('M d, Y h:i A') ?? 'N/A' }}</span>
                </div>
                @if ($order->customer)
                    <div class="info-row">
                        <span class="label">Customer:</span>
                        <span class="value">
                            {{ $order->customer->name }}
                            ({{ ucfirst($order->customer->customer_type) }})
                        </span>
                    </div>
                @endif
            </div>

            {{-- Items Table --}}
            <div class="receipt-items">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $item->product?->name ?? 'Deleted Product' }}</td>
                                <td>{{ $item->quantity ?? 0 }}</td>
                                <td>
                                    @if (is_numeric($item->unit_price) && floor($item->unit_price) == $item->unit_price)
                                        {{ number_format($item->unit_price, 0) }}
                                    @else
                                        {{ number_format($item->unit_price ?? 0, 2) }}
                                    @endif
                                </td>
                                <td>
                                    @if (is_numeric($item->total_price) && floor($item->total_price) == $item->total_price)
                                        {{ number_format($item->total_price, 0) }}
                                    @else
                                        {{ number_format($item->total_price ?? 0, 2) }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="receipt-totals">
                <div class="total-row">
                    <span class="label">Subtotal</span>
                    <span class="value">
                        @if (is_numeric($order->subtotal) && floor($order->subtotal) == $order->subtotal)
                            {{ number_format($order->subtotal, 0) }}
                        @else
                            {{ number_format($order->subtotal ?? 0, 2) }}
                        @endif
                    </span>
                </div>

                <div class="total-row">
                    <span class="label">Tax ({{ $order->tax_rate ?? 0 }}%)</span>
                    <span class="value">
                        @if (is_numeric($order->tax) && floor($order->tax) == $order->tax)
                            {{ number_format($order->tax, 0) }}
                        @else
                            {{ number_format($order->tax ?? 0, 2) }}
                        @endif
                    </span>
                </div>

                @if (($order->delivery_charges ?? 0) > 0)
                    <div class="total-row">
                        <span class="label">Delivery Charges</span>
                        <span class="value">
                            @if (is_numeric($order->delivery_charges) && floor($order->delivery_charges) == $order->delivery_charges)
                                {{ number_format($order->delivery_charges, 0) }}
                            @else
                                {{ number_format($order->delivery_charges ?? 0, 2) }}
                            @endif
                        </span>
                    </div>
                @endif

                @if (($order->discount ?? 0) > 0)
                    <div class="total-row discount">
                        <span class="label">Discount</span>
                        <span class="value">
                            -@if (is_numeric($order->discount) && floor($order->discount) == $order->discount)
                                {{ number_format($order->discount, 0) }}
                            @else
                                {{ number_format($order->discount ?? 0, 2) }}
                            @endif
                        </span>
                    </div>
                @endif

                <div class="total-row grand">
                    <span class="label">Total Bill</span>
                    <span class="value">Rs. {{ number_format($order->total ?? 0, 0) }}</span>
                </div>

                {{-- Payment / Balance rows — only show if customer has any khata/balance record --}}
                @php
                    $paidAmount = $order->paid_amount ?? $order->total;
                    $balanceOnBill = $order->balance_amount ?? 0;
                    $prevBalance = $order->previous_balance ?? 0;
                    $currentBalance = $prevBalance + ($balanceOnBill ?: ($order->total - $paidAmount));
                    $hasKhata = $balanceOnBill > 0 || $prevBalance > 0 || $paidAmount < $order->total;
                @endphp

                @if ($hasKhata)
                    <div class="total-row paid">
                        <span class="label">Amount Paid</span>
                        <span class="value">Rs. {{ number_format($paidAmount, 0) }}</span>
                    </div>

                    @if ($balanceOnBill > 0)
                        <div class="total-row balance">
                            <span class="label">Balance on Bill</span>
                            <span class="value">Rs. {{ number_format($balanceOnBill, 0) }}</span>
                        </div>
                    @endif

                    @if ($prevBalance > 0)
                        <div class="total-row prev-balance">
                            <span class="label">Previous Balance</span>
                            <span class="value">Rs. {{ number_format($prevBalance, 0) }}</span>
                        </div>
                    @endif

                    @if ($currentBalance > 0)
                        <div class="total-row due">
                            <span class="label">Current Balance Due</span>
                            <span class="value">Rs. {{ number_format($currentBalance, 0) }}</span>
                        </div>
                    @elseif ($currentBalance < 0)
                        <div class="total-row advance">
                            <span class="label">Advance Credit</span>
                            <span class="value">Rs. {{ number_format(abs($currentBalance), 0) }}</span>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Payment & Dispatch meta --}}
            <div class="receipt-meta">
                <div class="info-row">
                    <span class="label">Payment Method:</span>
                    <span class="value">{{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}</span>
                </div>
                @if ($order->dispatch_method)
                    <div class="info-row">
                        <span class="label">Dispatch:</span>
                        <span class="value">{{ $order->dispatch_method }}</span>
                    </div>
                @endif
                @if ($order->tracking_id)
                    <div class="info-row">
                        <span class="label">Tracking ID:</span>
                        <span class="value">{{ $order->tracking_id }}</span>
                    </div>
                @endif
                @if ($order->weight)
                    <div class="info-row">
                        <span class="label">Parcel Weight:</span>
                        <span class="value">{{ $order->weight }} kg</span>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="receipt-footer">
                <p>Thank you for your business!</p>
                <p>Items can be returned within 7 days with receipt</p>
                <div class="qr-block">
                    <p>Scan to view receipt online:</p>
                    <div id="qrcode-container"></div>
                    <p class="receipt-url">{{ $order->receipt_url }}</p>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Action Buttons ── --}}
    <div class="receipt-actions no-print">
        <div class="action-grid">
            <a href="{{ $order->receipt_url }}" target="_blank" class="action-btn view">
                <i class="fas fa-eye"></i> View
            </a>
            <button id="whatsapp-share-btn" class="action-btn whatsapp">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </button>
            <button onclick="window.print()" class="action-btn print">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="{{ route('admin.pos.receipt.thermal', $order) }}" target="_blank" class="action-btn print" style="background:#0d9488;">
                <i class="fas fa-receipt"></i> Thermal
            </a>
            <a href="{{ route('admin.pos.receipt.pdf', $order) }}" class="action-btn pdf">
                <i class="fas fa-download"></i> PDF
            </a>
            <button id="copy-link-btn" class="action-btn copy">
                <i class="fas fa-copy"></i> Copy Link
            </button>
            <a href="{{ route('admin.pos.edit', $order) }}" class="action-btn edit">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.pos.index') }}" class="action-btn new-sale" style="grid-column: span 2;">
                <i class="fas fa-plus"></i> New Sale
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ── QR Code ──
            const receiptUrl = "{{ $order->receipt_url }}";
            if (receiptUrl) {
                try {
                    const container = document.getElementById('qrcode-container');
                    container.innerHTML = '';
                    const wrapper = document.createElement('div');
                    wrapper.style.cssText = 'width:100px;height:100px;position:relative;';
                    container.appendChild(wrapper);

                    new QRCode(wrapper, {
                        text: receiptUrl,
                        width: 100,
                        height: 100,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });

                    // Clean up duplicate elements QRCode.js creates
                    setTimeout(() => {
                        const imgs = wrapper.getElementsByTagName('img');
                        for (let i = 1; i < imgs.length; i++) imgs[i].remove();
                    }, 100);
                } catch (e) {
                    document.getElementById('qrcode-container').innerHTML =
                        `<p style="font-size:10px;color:#9ca3af;text-align:center;">QR unavailable</p>`;
                }
            }

            // ── WhatsApp Share ──
            document.getElementById('whatsapp-share-btn').addEventListener('click', function(e) {
                e.preventDefault();

                let customerName = "{{ $order->customer?->name ?? 'Customer' }}";
                let phone = "{{ $order->customer?->phone ?? '' }}";

                if (!phone) {
                    phone = prompt("Enter customer phone number (with country code, e.g., 923001234567):", "92");
                    if (!phone) { alert("Phone number is required."); return; }
                }

                phone = phone.replace(/\D/g, '');
                if (!phone.startsWith('92')) {
                    phone = phone.startsWith('0') ? '92' + phone.substring(1) : '92' + phone;
                }

                let message = `*AlMufeed Saqafti Markaz - Receipt*\n\n`;
                message += `Dear ${customerName},\n\n`;
                message += `Thank you for shopping with us!\n\n`;
                message += `*Receipt #*: {{ $order->order_number }}\n`;
                message += `*Date*: {{ $order->created_at?->format('d M, Y h:i A') }}\n`;
                message += `*Total Bill*: Rs. {{ number_format($order->total ?? 0, 0) }}\n`;
                @if ($hasKhata)
                    message += `*Amount Paid*: Rs. {{ number_format($paidAmount, 0) }}\n`;
                    @if ($balanceOnBill > 0)
                        message += `*Balance on Bill*: Rs. {{ number_format($balanceOnBill, 0) }}\n`;
                    @endif
                    @if ($prevBalance > 0)
                        message += `*Previous Balance*: Rs. {{ number_format($prevBalance, 0) }}\n`;
                    @endif
                    @if ($currentBalance > 0)
                        message += `\n*Total Balance Due*: Rs. {{ number_format($currentBalance, 0) }}\n`;
                    @elseif ($currentBalance < 0)
                        message += `\n*Advance Credit*: Rs. {{ number_format(abs($currentBalance), 0) }}\n`;
                    @endif
                @endif
                message += `\n*Payment Method*: {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}\n`;
                @if ($order->dispatch_method)
                    message += `*Dispatch Method*: {{ $order->dispatch_method }}\n`;
                @endif
                @if ($order->tracking_id)
                    message += `*Tracking ID*: {{ $order->tracking_id }}\n`;
                    @if ($order->weight)
                        message += `*Weight*: {{ $order->weight }} kg\n`;
                    @endif
                @endif
                message += `\n*View Your Receipt Online:*\n${receiptUrl}\n\n`;
                message += `*Contact Us:*\n03007951919\nwww.almufeed.com.pk\n\nWe appreciate your purchase!`;

                window.open(`https://api.whatsapp.com/send?phone=${phone}&text=${encodeURIComponent(message)}`, '_blank');
            });

            // ── Copy Link ──
            document.getElementById('copy-link-btn').addEventListener('click', async function() {
                const btn = this;
                try {
                    await navigator.clipboard.writeText(receiptUrl);
                } catch {
                    const t = document.createElement('input');
                    t.value = receiptUrl;
                    document.body.appendChild(t);
                    t.select();
                    document.execCommand('copy');
                    document.body.removeChild(t);
                }
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                btn.style.background = '#16a34a';
                setTimeout(() => { btn.innerHTML = orig; btn.style.background = ''; }, 2000);
            });
        });
    </script>
@endpush
