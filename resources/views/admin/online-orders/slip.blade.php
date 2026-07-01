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
        .cell { border: 1px solid #111827; }

        /* Header (orange framed) : courier logo | title | company logo */
        .head { display: flex; border: 3px solid #e07b2c; }
        .head > div { padding: 6px 10px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; }
        .head .courier { flex: 1; border-right: 2px solid #e07b2c; }
        .head .title   { flex: 1.4; }
        .head .brand   { flex: 1; border-left: 2px solid #e07b2c; }
        .head .title .t { font-size: 22px; font-weight: 800; letter-spacing: .01em; }
        .head .title .sub { font-size: 12px; font-weight: 700; color: #111827; margin-top: 2px; }
        .head .logo { max-height: 46px; max-width: 150px; object-fit: contain; display: block; margin: 0 auto; }
        .head .cname { font-size: 12px; font-weight: 800; margin-top: 2px; }
        .head .ph { font-size: 11px; color: #6b7280; font-weight: 700; }

        /* Row of 3 : tracking+barcode | order no | date */
        .row3 { display: flex; border-left: 2px solid #111827; border-right: 2px solid #111827; }
        .row3 > div { flex: 1; padding: 5px 8px; border-bottom: 2px solid #111827; text-align: center; }
        .row3 > div + div { border-left: 1px solid #111827; }
        .row3 .k { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; }
        .row3 .track svg { max-width: 210px; height: 34px; }
        .row3 .trackno { font-size: 12px; font-weight: 800; letter-spacing: .04em; }
        .row3 .ordno { font-size: 18px; font-weight: 800; }
        .row3 .date { font-size: 14px; font-weight: 700; margin-top: 4px; }
        .row3 .empty { font-size: 11px; color: #9ca3af; padding: 8px 0; }

        /* Main 3 columns : from | to | codes/parcel */
        .main { display: flex; border-left: 2px solid #111827; border-right: 2px solid #111827; }
        .main > div { padding: 7px 10px; }
        .main .from { flex: 1; border-right: 1px solid #111827; display: flex; flex-direction: column; }
        .main .to   { flex: 1.5; border-right: 1px solid #111827; }
        .main .side { flex: 1; padding: 0; display: flex; flex-direction: column; }
        .lead { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; }

        .to .cname { font-size: 20px; font-weight: 800; line-height: 1.15; }
        .to .cphone { font-size: 15px; font-weight: 800; margin-top: 2px; }
        .to .caddr { font-size: 14px; font-weight: 600; margin-top: 4px; line-height: 1.4; }
        .to .cmeta { font-size: 13px; margin-top: 5px; }
        .to .cmeta b { font-weight: 800; }

        .from .fname { font-size: 14px; font-weight: 800; margin-top: 2px; }
        .from .fname.urdu { font-size: 15px; }
        .from .fbody { font-size: 12px; font-weight: 600; margin-top: 3px; line-height: 1.4; }
        .from .remarks { margin-top: auto; border-top: 1px dashed #9ca3af; padding-top: 4px; }
        .from .remarks .k { font-size: 10px; font-weight: 800; color: #6b7280; }
        .from .remarks .space { height: 34px; }

        .side .cod { border-bottom: 1px solid #111827; padding: 7px 10px; text-align: center; }
        .side .cod.due { background: #fff7ed; }
        .side .cod .amt { font-size: 22px; font-weight: 800; line-height: 1.1; }
        .side .cod .tag { font-size: 11px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; color: #b45309; }
        .side .cod .paid { color: #059669; }
        .side .cod svg { max-width: 180px; height: 30px; margin-top: 3px; }
        .side .parcel { flex: 1; padding: 7px 10px; display: flex; align-items: center; justify-content: space-between; gap: 8px; }
        .side .parcel .facts { font-size: 12px; font-weight: 700; line-height: 1.5; }
        .side .parcel .facts b { font-size: 15px; font-weight: 800; }
        .side .parcel .qrwrap { text-align: center; }
        .side .parcel .qrwrap .k { font-size: 9px; font-weight: 800; color: #6b7280; }

        .note { border: 2px solid #111827; border-top: none; padding: 6px 10px; text-align: center; }
        .note .k { font-size: 10px; font-weight: 800; text-transform: uppercase; color: #6b7280; }
        .note .urdu { font-size: 12px; }
        .note .en { font-size: 11px; }

        .footer { background: #111827; color: #fff; padding: 6px 10px; font-size: 11px; display: flex; align-items: center; justify-content: center; gap: 16px; flex-wrap: wrap; }
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

    // Per-branch slip (#audio3, prev): the order's own branch supplies the logo +
    // contact, falling back to the global website/brand settings.
    $branch     = $order->branch;
    $branchLogo = $branch?->logo ? asset('storage/'.$branch->logo) : asset('assets/images/brand/almufeed-traders.png');
    $company = [
        'name'    => $branch?->name ?: setting('site_name', 'AL MUFEED TRADERS'),
        'name_ur' => setting('site_name_ur', 'المفید اسلامی ثقافتی مرکز'),
        'phone'   => $branch?->phone ?: setting('site_phone'),
        'addr'    => $branch?->address ?: setting('site_address'),
        'website' => setting('site_website', 'www.almufeed.com.pk'),
    ];

    // Reseller "From": only rendered when the reseller supplied an address.
    $isReseller = $from === 'reseller' && $order->from_name;
    $sender = $isReseller
        ? ['name' => $order->from_name, 'name_ur' => $order->from_name, 'phone' => $order->from_phone, 'addr' => $order->from_address]
        : ['name' => $company['name'], 'name_ur' => $company['name_ur'], 'phone' => $company['phone'], 'addr' => $company['addr']];

    // ── Payment / COD (#audio3): if the order is already settled, it is NOT a
    // COD parcel and no amount is collected on delivery. COD amount = balance due.
    $isPaid = in_array($order->online_payment_status, ['paid', 'bank_paid'], true)
        || $order->payment_status === 'paid'
        || (float) $order->balance_amount <= 0;
    $codByMethod = ($order->online_payment_status === 'cod')
        || (bool) \App\Models\PaymentMethod::where('name', $order->payment_method)->value('is_cod');
    $isCod  = $codByMethod && ! $isPaid;
    $codAmt = $isCod ? (float) $order->balance_amount : 0.0;

    // Barcodes print ONLY once (no doubling — #audio2): tracking barcode + number,
    // and a COD-amount barcode. Order number shows once, in its own box.
    $hasTracking = (bool) $order->tracking_id;

    // QR — scannable, opens the order's public tracking / payment page.
    $payUrl = $order->receipt_token ? route('shop.track.view', $order->receipt_token) : url('/');

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
        <a href="{{ $q(['lang'=>'both']) }}" class="{{ $lang==='both'?'on':'' }}">Both</a>
        <a href="{{ $q(['lang'=>'en']) }}" class="{{ $lang==='en'?'on':'' }}">English</a>
        <a href="{{ $q(['lang'=>'ur']) }}" class="{{ $lang==='ur'?'on':'' }}">اردو</a>
        <span style="width:1px;height:18px;background:#e5e7eb;"></span>
        <strong style="font-size:12px;">From:</strong>
        <a href="{{ $q(['from'=>'company']) }}" class="{{ $from==='company'?'on':'' }}">Company</a>
        @if ($order->from_name)
            <a href="{{ $q(['from'=>'reseller']) }}" class="{{ $from==='reseller'?'on':'' }}">Reseller</a>
        @endif
        @if (! $hasTracking)
            <span style="font-size:11px;color:#b45309;background:#fffbeb;border:1px solid #fde68a;padding:4px 8px;border-radius:6px;">⚠ Add a tracking number to print the barcode</span>
        @endif
        <button class="print" onclick="window.print()">🖨 Print</button>
        <a href="{{ route('admin.online-orders.show', $order) }}">← Back</a>
    </div>

    <div class="sheet">
        {{-- ── Header: courier logo · title · company logo ── --}}
        <div class="head">
            <div class="courier">
                @if ($dispatchMethod?->logo)
                    <img class="logo" src="{{ asset('storage/'.$dispatchMethod->logo) }}" alt="" onerror="this.style.display='none'">
                @else
                    <div class="ph urdu">کوریئر کمپنی لوگو</div>
                @endif
                <div class="cname">{{ $order->dispatch_method ?: 'Courier' }}</div>
            </div>
            <div class="title">
                <div class="t">Dispatch Slip</div>
                <div class="sub">{{ $isCod ? 'COD Parcel' : 'General Parcel' }}</div>
            </div>
            <div class="brand">
                <img class="logo" src="{{ $branchLogo }}" alt="" onerror="this.style.display='none'">
                <div class="cname urdu">{{ $company['name_ur'] }}</div>
            </div>
        </div>

        {{-- ── Row: Tracking ID + barcode · Order No · Date ── --}}
        <div class="row3">
            <div class="track">
                <div class="k">{!! $bi('Tracking ID', 'ٹریکنگ نمبر') !!}</div>
                @if ($hasTracking)
                    <div><svg id="barcode-track"></svg></div>
                    <div class="trackno">{{ $order->tracking_id }}</div>
                @else
                    <div class="empty">{{ $showUr ? 'ٹریکنگ نمبر بعد میں' : '—' }}</div>
                @endif
            </div>
            <div>
                <div class="k">{!! $bi('Order No', 'آرڈر نمبر') !!}</div>
                <div class="ordno">{{ $order->order_number }}</div>
            </div>
            <div>
                <div class="k">{!! $bi('Date', 'تاریخ') !!}</div>
                <div class="date">{{ $order->created_at?->format('d M Y') }}</div>
            </div>
        </div>

        {{-- ── Main: From · To · COD/Parcel ── --}}
        <div class="main">
            {{-- FROM (reseller / company) + manual remarks box --}}
            <div class="from">
                <div class="lead">{{ $showUr ? 'مرسل / From' : 'From' }}</div>
                <div class="fname {{ $isReseller ? '' : 'urdu' }}">{{ $isReseller ? $sender['name'] : $sender['name_ur'] }}</div>
                <div class="fbody">
                    @if ($sender['phone'])☎ {{ $sender['phone'] }}<br>@endif
                    {{ $sender['addr'] }}
                </div>
                <div class="remarks">
                    <div class="k">{!! $bi('Remarks', 'ریمارکس') !!}</div>
                    <div class="space"></div>
                </div>
            </div>

            {{-- TO — stylish customer block (#audio2) --}}
            <div class="to">
                <div class="lead">{{ $showUr ? 'وصول کنندہ / To' : 'To' }}</div>
                <div class="cname">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</div>
                <div class="cphone">☎ {{ $order->shipping_phone }}</div>
                <div class="caddr">
                    {{ $order->shipping_address1 }}@if ($order->shipping_address2), {{ $order->shipping_address2 }}@endif
                </div>
                <div class="cmeta">
                    @if ($order->shipping_tehsil)<b>{!! $bi('Tehsil', 'تحصیل') !!}:</b> {{ $order->shipping_tehsil }} &nbsp; @endif
                    <b>{!! $bi('District/City', 'ضلع/شہر') !!}:</b> {{ collect([$order->shipping_district, $order->shipping_city])->filter()->implode(' / ') ?: '—' }}
                    @if ($order->shipping_post_code) — {{ $order->shipping_post_code }}@endif
                    <br>
                    <b>{!! $bi('Province', 'صوبہ') !!}:</b> {{ $order->shipping_province ?: '—' }} ({{ $order->shipping_country ?: 'Pakistan' }})
                </div>
            </div>

            {{-- COD amount + barcode, then parcel facts + QR --}}
            <div class="side">
                <div class="cod {{ $isCod ? 'due' : '' }}">
                    @if ($isCod)
                        <div class="tag">{!! $bi('COD Amount', 'وصولی رقم') !!}</div>
                        <div class="amt">Rs. {{ number_format($codAmt, 0) }}</div>
                        <div><svg id="barcode-cod"></svg></div>
                    @else
                        <div class="tag paid">{!! $bi('Prepaid — No COD', 'ادا شدہ') !!}</div>
                        <div class="amt paid">Rs. 0</div>
                    @endif
                </div>
                <div class="parcel">
                    <div class="facts">
                        <div>{!! $bi('Pieces', 'تعداد') !!}: <b>{{ $pieces }}</b> {{ $showEn ? 'in 1 parcel' : '' }}</div>
                        <div>{!! $bi('Weight', 'وزن') !!}: <b>{{ $weightTxt }}</b></div>
                    </div>
                    <div class="qrwrap">
                        <div id="qr-pay"></div>
                        <div class="k">{!! $bi('Scan', 'اسکین') !!}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Postman note (full width) ── --}}
        <div class="note">
            <div class="k">{!! $bi('Note for postman', 'ڈاک صاحب کے لیے' ) !!}</div>
            @if ($showUr)<div class="urdu">{{ $postmanUr }}</div>@endif
            @if ($showEn)<div class="en">{{ $postmanEn }}</div>@endif
        </div>

        {{-- ── Dark footer: company address · contact · web ── --}}
        <div class="footer">
            @if ($company['phone'])<span>☎ {{ $company['phone'] }}</span>@endif
            @if ($company['website'])<span>🌐 {{ $company['website'] }}</span>@endif
            @if ($company['addr'])<span>📍 {{ $company['addr'] }}</span>@endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if ($hasTracking)
            // Tracking barcode (Code128). The readable number prints once below it.
            try { JsBarcode('#barcode-track', @json((string) $order->tracking_id), { format: 'CODE128', width: 1.6, height: 34, displayValue: false, margin: 0 }); } catch (e) {}
            @endif
            @if ($isCod)
            // COD-amount barcode — encodes the rupee amount collected on delivery.
            try { JsBarcode('#barcode-cod', @json((string) (int) $codAmt), { format: 'CODE128', width: 1.4, height: 30, displayValue: false, margin: 0 }); } catch (e) {}
            @endif
            // QR = order's public tracking / payment page (scannable).
            try { new QRCode(document.getElementById('qr-pay'), { text: @json($payUrl), width: 70, height: 70, correctLevel: QRCode.CorrectLevel.M }); } catch (e) {}
        });
    </script>
</body>
</html>
