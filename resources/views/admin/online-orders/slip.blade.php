<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dispatch slip — {{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@500;700&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; margin: 0; background: #f3f4f6; color: #111827; }
        .urdu { font-family: 'Noto Nastaliq Urdu', serif; }

        .toolbar { background: #fff; border-bottom: 1px solid #e5e7eb; padding: 10px 16px; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
        .toolbar a, .toolbar button { font-size: 12px; padding: 6px 12px; border-radius: 8px; border: 1px solid #d1d5db; background: #fff; color: #374151; text-decoration: none; cursor: pointer; }
        .toolbar a.on { background: #0891b2; color: #fff; border-color: #0891b2; }
        .toolbar .print { background: #111827; color: #fff; border-color: #111827; margin-left: auto; }

        /* ── Landscape A5 sheet ── */
        .sheet { width: 210mm; margin: 16px auto; background: #fff; border: 2px solid #111827; }

        /* Header : courier logo | title (top) | company logo — NO dividers between */
        .head { display: flex; align-items: flex-start; border-bottom: 2px solid #111827; }
        .head > div { padding: 3px 10px 6px; display: flex; flex-direction: column; align-items: center; text-align: center; }
        .head .courier { flex: 1; }
        .head .title   { flex: 1.4; }
        .head .brand   { flex: 1; }
        .head .title .t { font-size: 24px; font-weight: 800; line-height: 1.05; }
        .head .title .sub { font-size: 12px; font-weight: 700; color: #111827; margin-top: 2px; }
        .head .clogo { max-height: 62px; max-width: 190px; object-fit: contain; margin-top: 4px; }
        .head .blogo { max-height: 46px; max-width: 160px; object-fit: contain; margin-top: 4px; }
        .head .cname { font-size: 13px; font-weight: 800; margin-top: 3px; }
        .head .ph { font-size: 11px; color: #6b7280; font-weight: 700; }

        /* Row : tracking+barcode | order no + QR | date */
        .row3 { display: flex; border-left: 2px solid #111827; border-right: 2px solid #111827; }
        .row3 > div { flex: 1; padding: 5px 8px; border-bottom: 2px solid #111827; text-align: center; }
        .row3 > div + div { border-left: 1px solid #111827; }
        .row3 .k { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; }
        .row3 .track { display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .row3 .track svg { max-width: 230px; height: 36px; }
        .row3 .track .trackno { font-size: 15px; font-weight: 800; letter-spacing: .05em; margin-top: 1px; }
        .row3 .track .empty { font-size: 11px; color: #9ca3af; padding: 10px 0; }
        .row3 .orderbox { display: flex; align-items: center; justify-content: center; gap: 10px; }
        .row3 .orderbox .ordno { font-size: 19px; font-weight: 800; }
        .row3 .orderbox .oqr { display: flex; flex-direction: column; align-items: center; }
        .row3 .orderbox .oqr .k { font-size: 8px; }
        .row3 .date { font-size: 15px; font-weight: 700; margin-top: 3px; }
        .row3 .time { font-size: 12px; font-weight: 600; color: #374151; }

        /* Main : [from] | to | codes/parcel */
        .main { display: flex; border-left: 2px solid #111827; border-right: 2px solid #111827; border-bottom: 2px solid #111827; }
        .main > div { padding: 7px 10px; }
        .main .from { flex: 1; border-right: 1px solid #111827; }
        .main .to   { flex: 1.7; border-right: 1px solid #111827; }
        .main .side { flex: 1; padding: 0; display: flex; flex-direction: column; }
        .lead { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; }

        .to .cname { font-size: 21px; font-weight: 800; line-height: 1.15; }
        .to .cphone { font-size: 16px; font-weight: 800; margin-top: 2px; }
        .to .caddr { font-size: 15px; font-weight: 600; margin-top: 4px; line-height: 1.4; }
        .to .cmeta { font-size: 13px; margin-top: 5px; line-height: 1.5; }
        .to .cmeta b { font-weight: 800; }

        .from .fname { font-size: 15px; font-weight: 800; margin-top: 2px; }
        .from .fbody { font-size: 12px; font-weight: 600; margin-top: 3px; line-height: 1.4; }

        .side .cod { border-bottom: 1px solid #111827; padding: 7px 10px; text-align: center; }
        .side .cod.due { background: #fff7ed; }
        .side .cod .amt { font-size: 23px; font-weight: 800; line-height: 1.1; }
        .side .cod .tag { font-size: 11px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; color: #b45309; }
        .side .cod .amt.paid { color: #059669; }
        .side .cod svg { max-width: 180px; height: 30px; margin-top: 3px; }
        .side .parcel { flex: 1; padding: 7px 10px; display: flex; align-items: center; justify-content: space-between; gap: 8px; }
        .side .parcel .facts { font-size: 13px; font-weight: 700; line-height: 1.6; }
        .side .parcel .facts b { font-size: 16px; font-weight: 800; }
        .side .parcel .wqr { text-align: center; }
        .side .parcel .wqr .wt { font-size: 12px; font-weight: 800; margin-top: 1px; }

        /* Remarks — separate manual (handwriting) box below the address */
        .remarks { border-left: 2px solid #111827; border-right: 2px solid #111827; border-bottom: 2px solid #111827; padding: 5px 10px; }
        .remarks .k { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; }
        .remarks .space { height: 26px; }
        .remarks .rtext { font-size: 13px; font-weight: 600; margin-top: 2px; line-height: 1.4; }

        /* Postman note — full width, no heading label */
        .note { border-left: 2px solid #111827; border-right: 2px solid #111827; border-bottom: 2px solid #111827; padding: 7px 14px; text-align: center; }
        .note .urdu { font-size: 13px; line-height: 1.6; }
        .note .en { font-size: 12px; line-height: 1.5; }

        /* Dark footer — spread across the full line */
        .footer { background: #111827; color: #fff; padding: 8px 16px; font-size: 12px; display: flex; align-items: center; justify-content: space-around; gap: 14px; flex-wrap: wrap; }
        .footer span { white-space: nowrap; }

        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { margin: 0; border: 2px solid #111827; width: auto; }
            @page { size: A5 landscape; margin: 6mm; }
        }
    </style>
</head>
@php
    $showEn = $lang !== 'ur';
    $showUr = $lang !== 'en';

    // Per-branch slip: the order's own branch supplies the logo + contact,
    // falling back to the global website/brand settings.
    $branch     = $order->branch;
    $branchLogo = $branch?->logo ? asset('storage/'.$branch->logo) : asset('assets/images/brand/almufeed-traders.png');
    $company = [
        'name'    => $branch?->name ?: setting('site_name', 'AL MUFEED TRADERS'),
        'name_ur' => setting('site_name_ur', 'المفید اسلامی ثقافتی مرکز'),
        'phone'   => $branch?->phone ?: setting('site_phone'),
        'addr'    => $branch?->address ?: setting('site_address'),
        'website' => setting('site_website', 'www.almufeed.com.pk'),
    ];

    // "From" prints ONLY for a reseller who supplied their own address; a normal
    // customer order hides the From column entirely.
    $isReseller = $from === 'reseller' && $order->from_name;
    $sender = $isReseller
        ? ['name' => $order->from_name, 'phone' => $order->from_phone, 'addr' => $order->from_address]
        : null;

    // ── COD amount to collect on delivery. Operator can override it on the order
    // page (dispatch_cod_amount); blank falls back to auto: paid → 0, else balance.
    $isPaid = in_array($order->online_payment_status, ['paid', 'bank_paid'], true)
        || $order->payment_status === 'paid'
        || (float) $order->balance_amount <= 0;
    $codAmt = $order->dispatch_cod_amount !== null
        ? (float) $order->dispatch_cod_amount
        : ($isPaid ? 0.0 : (float) $order->balance_amount);
    $isCod  = $codAmt > 0;

    $hasTracking = (bool) $order->tracking_id;

    // Order-tracking QR — scannable, opens the order's public tracking page.
    $payUrl    = $order->receipt_token ? route('shop.track.view', $order->receipt_token) : url('/');
    $weightTxt = (rtrim(rtrim(number_format((float) $order->weight, 3), '0'), '.') ?: '0') . ' kg';
    $pieces    = (int) $order->items->sum('quantity');

    $postmanEn = setting('dispatch_postman_note', 'Dear postman: if delivering this parcel is difficult, please call ' . ($company['phone'] ?: '') . ' — but kindly ensure delivery. Thank you.');
    $postmanUr = setting('dispatch_postman_note_ur', 'محترم ڈاک صاحب! اگر پارسل وصول کنندہ تک پہنچانے میں کوئی مشکل ہو تو ' . ($company['phone'] ?: '') . ' اس نمبر پر رابطہ کریں مگر پارسل کی ڈیلیوری یقینی بنائیں۔ شکریہ');

    $base = url()->current();
    $q = fn($params) => $base . '?' . http_build_query(array_merge(request()->query(), $params));

    // Bilingual inline label helper.
    $bi = function ($en, $ur) use ($showEn, $showUr) {
        $parts = [];
        if ($showUr) $parts[] = '<span class="urdu">' . e($ur) . '</span>';
        if ($showEn) $parts[] = '<span>' . e($en) . '</span>';
        return implode(' / ', $parts);
    };
@endphp
<body>
    <div class="toolbar">
        <strong style="font-size:12px;">Language:</strong>
        <a href="{{ $q(['lang'=>'en']) }}" class="{{ $lang==='en'?'on':'' }}">English</a>
        <a href="{{ $q(['lang'=>'both']) }}" class="{{ $lang==='both'?'on':'' }}">Both</a>
        <a href="{{ $q(['lang'=>'ur']) }}" class="{{ $lang==='ur'?'on':'' }}">اردو</a>
        @if ($order->from_name)
            <span style="width:1px;height:18px;background:#e5e7eb;"></span>
            <strong style="font-size:12px;">From:</strong>
            <a href="{{ $q(['from'=>'company']) }}" class="{{ $from!=='reseller'?'on':'' }}">Hide</a>
            <a href="{{ $q(['from'=>'reseller']) }}" class="{{ $from==='reseller'?'on':'' }}">Reseller</a>
        @endif
        @if (! $hasTracking)
            <span style="font-size:11px;color:#b45309;background:#fffbeb;border:1px solid #fde68a;padding:4px 8px;border-radius:6px;">⚠ Add a tracking number to print the barcode</span>
        @endif
        <button class="print" onclick="window.print()">🖨 Print</button>
        <a href="{{ route('admin.online-orders.show', $order) }}">← Back</a>
    </div>

    <div class="sheet">
        {{-- ── Header: courier logo · title (top) · company logo — no dividers ── --}}
        <div class="head">
            <div class="courier">
                @if ($dispatchMethod?->logo)
                    <img class="clogo" src="{{ asset('storage/'.$dispatchMethod->logo) }}" alt="" onerror="this.style.display='none'">
                @else
                    <div class="ph urdu">کوریئر</div>
                    <div class="cname">{{ $order->dispatch_method ?: 'Courier' }}</div>
                @endif
            </div>
            <div class="title">
                <div class="t">Dispatch Slip</div>
                <div class="sub">{{ $isCod ? 'COD Parcel' : 'General Parcel' }}</div>
            </div>
            <div class="brand">
                <img class="blogo" src="{{ $branchLogo }}" alt="" onerror="this.style.display='none'">
                <div class="cname urdu">{{ $company['name_ur'] }}</div>
            </div>
        </div>

        {{-- ── Row: Tracking+barcode · Order No + QR · Date ── --}}
        <div class="row3">
            <div class="track">
                @if ($hasTracking)
                    <svg id="barcode-track"></svg>
                    <div class="trackno">{{ $showUr ? 'ٹریکنگ' : 'Tracking' }} {{ $order->tracking_id }}</div>
                @else
                    <div class="empty">{{ $showUr ? 'ٹریکنگ نمبر بعد میں' : 'Tracking added later' }}</div>
                @endif
            </div>
            <div>
                <div class="k">{!! $bi('Order No', 'آرڈر نمبر') !!}</div>
                <div class="orderbox">
                    <div class="ordno">{{ $order->order_number }}</div>
                    <div class="oqr">
                        <div id="qr-track"></div>
                        <div class="k">{{ $showUr ? 'ٹریک' : 'Track' }}</div>
                    </div>
                </div>
            </div>
            <div>
                <div class="k">{!! $bi('Date', 'تاریخ') !!}</div>
                <div class="date">{{ $order->created_at?->format('d M Y') }}</div>
                <div class="time">{{ $order->created_at?->format('h:i A') }}</div>
            </div>
        </div>

        {{-- ── Main: [From — reseller only] · To · COD/Parcel ── --}}
        <div class="main">
            @if ($isReseller)
                <div class="from">
                    <div class="lead">{{ $showUr ? 'مرسل / From' : 'From' }}</div>
                    <div class="fname">{{ $sender['name'] }}</div>
                    <div class="fbody">
                        @if ($sender['phone'])☎ {{ $sender['phone'] }}<br>@endif
                        {{ $sender['addr'] }}
                    </div>
                </div>
            @endif

            {{-- TO — stylish customer block --}}
            <div class="to">
                <div class="lead">{{ $showUr ? 'وصول کنندہ / To' : 'To' }}</div>
                <div class="cname">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</div>
                <div class="cphone">☎ {{ $order->shipping_phone }}</div>
                <div class="caddr">
                    {{ $order->shipping_address1 }}@if ($order->shipping_address2), {{ $order->shipping_address2 }}@endif
                </div>
                <div class="cmeta">
                    @if ($order->shipping_tehsil)<b>{!! $bi('Tehsil', 'تحصیل') !!}:</b> {{ $order->shipping_tehsil }} &nbsp; @endif
                    <b>{!! $bi('District', 'ضلع') !!}:</b> {{ $order->shipping_district ?: $order->shipping_city ?: '—' }}
                    @if ($order->shipping_post_code) — {{ $order->shipping_post_code }}@endif
                    <br>
                    <b>{!! $bi('Province', 'صوبہ') !!}:</b> {{ $order->shipping_province ?: '—' }} ({{ $order->shipping_country ?: 'Pakistan' }})
                </div>
            </div>

            {{-- COD amount + barcode, then parcel facts + weight QR --}}
            <div class="side">
                <div class="cod {{ $isCod ? 'due' : '' }}">
                    <div class="tag">{!! $bi('COD Amount', 'وصولی رقم') !!}</div>
                    <div class="amt {{ $isCod ? '' : 'paid' }}">Rs. {{ number_format($codAmt, 0) }}</div>
                </div>
                <div class="parcel">
                    <div class="facts">
                        <div>{!! $bi('Pieces', 'تعداد') !!}: <b>{{ $pieces }}</b> {{ $showEn ? 'in 1 parcel' : '' }}</div>
                    </div>
                    <div class="wqr">
                        <div id="qr-weight"></div>
                        <div class="wt">{{ $showUr ? 'وزن' : 'Weight' }}: {{ $weightTxt }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Remarks — only prints when the operator typed something ── --}}
        @if (trim((string) $order->dispatch_remarks) !== '')
            <div class="remarks">
                <span class="k">{!! $bi('Remarks', 'ریمارکس') !!}</span>
                <div class="rtext">{{ $order->dispatch_remarks }}</div>
            </div>
        @endif

        {{-- ── Postman note (full width, no heading) ── --}}
        <div class="note">
            @if ($showUr)<div class="urdu">{{ $postmanUr }}</div>@endif
            @if ($showEn)<div class="en">{{ $postmanEn }}</div>@endif
        </div>

        {{-- ── Dark footer: contact · web · address spread across the line ── --}}
        <div class="footer">
            @if ($company['phone'])<span>☎ {{ $company['phone'] }}</span>@endif
            @if ($company['website'])<span>🌐 {{ $company['website'] }}</span>@endif
            @if ($company['addr'])<span>📍 {{ $company['addr'] }}</span>@endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if ($hasTracking)
            // Tracking barcode (Code128); readable number prints once below it.
            try { JsBarcode('#barcode-track', @json((string) $order->tracking_id), { format: 'CODE128', width: 1.6, height: 36, displayValue: false, margin: 0 }); } catch (e) {}
            @endif
            // Order-tracking QR — sits beside the order number.
            try { new QRCode(document.getElementById('qr-track'), { text: @json($payUrl), width: 58, height: 58, correctLevel: QRCode.CorrectLevel.M }); } catch (e) {}
            // Weight QR — encodes the parcel weight, with the weight printed below.
            try { new QRCode(document.getElementById('qr-weight'), { text: @json($weightTxt), width: 56, height: 56, correctLevel: QRCode.CorrectLevel.M }); } catch (e) {}
        });
    </script>
</body>
</html>
