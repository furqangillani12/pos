<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checklist — {{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; margin: 0; background: #f3f4f6; color: #111827; }
        .toolbar { background: #fff; border-bottom: 1px solid #e5e7eb; padding: 10px 16px; display: flex; gap: 8px; }
        .toolbar button, .toolbar a { font-size: 12px; padding: 6px 12px; border-radius: 8px; border: 1px solid #d1d5db; background: #fff; color: #374151; text-decoration: none; cursor: pointer; }
        .toolbar .print { background: #111827; color: #fff; border-color: #111827; margin-left: auto; }
        .sheet { max-width: 800px; margin: 16px auto; background: #fff; border: 1px solid #d1d5db; padding: 24px; }
        h1 { font-size: 18px; margin: 0 0 2px; }
        .muted { color: #6b7280; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: .06em; color: #6b7280; border-bottom: 2px solid #111827; padding: 6px; }
        td { border-bottom: 1px solid #e5e7eb; padding: 8px 6px; font-size: 13px; vertical-align: middle; }
        td img { width: 44px; height: 52px; object-fit: cover; border-radius: 4px; background: #f3f4f6; }
        .chk { width: 20px; height: 20px; border: 2px solid #9ca3af; border-radius: 4px; display: inline-block; }
        .code { font-family: monospace; font-size: 11px; color: #374151; }
        .totals { margin-top: 16px; text-align: right; font-size: 14px; }
        @media print { body { background: #fff; } .toolbar { display: none; } .sheet { margin: 0; border: none; max-width: none; } @page { margin: 12mm; } }
    </style>
</head>
<body>
    <div class="toolbar">
        <strong style="font-size:13px;align-self:center;">Picking checklist</strong>
        <button class="print" onclick="window.print()">🖨 Print</button>
        <a href="{{ route('admin.online-orders.show', $order) }}">← Back</a>
    </div>
    <div class="sheet">
        <h1>Order {{ $order->order_number }}</h1>
        <div class="muted">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }} · {{ $order->shipping_phone }} · {{ $order->created_at->format('d M Y') }}</div>

        <table>
            <thead>
                <tr>
                    <th style="width:30px;">✓</th>
                    <th style="width:56px;">Image</th>
                    <th>Product</th>
                    <th>Item code</th>
                    <th style="text-align:right;">Price</th>
                    <th style="text-align:center;">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td><input type="checkbox" class="pick" checked style="width:20px;height:20px;"></td>
                        <td><img src="{{ shop_image($item->product?->image) }}" alt=""></td>
                        <td>
                            <div style="font-weight:700;">{{ $item->product?->name ?? 'Product' }}</div>
                            @if ($item->product?->brand)<div class="muted">{{ $item->product->brand->name }}</div>@endif
                        </td>
                        <td><span class="code">{{ $item->product?->barcode ?: '—' }}</span></td>
                        <td style="text-align:right;">Rs. {{ number_format($item->unit_price, 0) }}</td>
                        <td style="text-align:center;">
                            <input type="number" class="qty" min="0" value="{{ (int) $item->quantity }}"
                                   style="width:60px;text-align:center;font-weight:800;font-size:15px;padding:4px;border:1px solid #d1d5db;border-radius:6px;">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div>Selected pieces: <strong id="selCount">{{ (int) $order->items->sum('quantity') }}</strong></div>
            <div>Order total: <strong>Rs. {{ number_format($order->total, 0) }}</strong></div>
        </div>

        {{-- Save the selected piece count to the dispatch slip (#11) --}}
        <div style="margin-top:16px;padding-top:14px;border-top:1px dashed #d1d5db;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <span class="muted">Tick the items and adjust quantities, then set the piece count that prints on the dispatch slip:</span>
            <button id="saveBtn" onclick="savePieces()"
                    style="background:#0891b2;color:#fff;border:none;border-radius:8px;padding:8px 14px;font-size:13px;font-weight:700;cursor:pointer;">
                Save <span id="saveCount"></span> pieces to slip
            </button>
            <span id="savedMsg" style="color:#059669;font-size:12px;font-weight:700;display:none;">✓ Saved</span>
            @if ($order->dispatch_pieces !== null)
                <span class="muted">Current slip pieces: <strong>{{ (int) $order->dispatch_pieces }}</strong></span>
            @endif
        </div>
    </div>

    <script>
        function selectedTotal() {
            let t = 0;
            document.querySelectorAll('tbody tr').forEach(function (tr) {
                const chk = tr.querySelector('.pick'), qty = tr.querySelector('.qty');
                if (chk && chk.checked && qty) t += (parseInt(qty.value) || 0);
            });
            return t;
        }
        function refresh() {
            const t = selectedTotal();
            document.getElementById('selCount').textContent = t;
            document.getElementById('saveCount').textContent = t;
            document.getElementById('savedMsg').style.display = 'none';
        }
        document.addEventListener('input', refresh);
        document.addEventListener('change', refresh);
        document.addEventListener('DOMContentLoaded', refresh);

        async function savePieces() {
            const t = selectedTotal();
            const fd = new FormData();
            fd.append('_method', 'PATCH');
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('dispatch_pieces', t);
            try {
                const res = await fetch('{{ route('admin.online-orders.pieces', $order) }}', {
                    method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    const m = document.getElementById('savedMsg');
                    m.textContent = '✓ Saved ' + t + ' pieces';
                    m.style.display = 'inline';
                }
            } catch (e) {}
        }
    </script>
</body>
</html>
