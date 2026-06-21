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
        .urdu { font-family: 'Noto Nastaliq Urdu', serif; direction: rtl; line-height: 2.2; }
        .toolbar { background: #fff; border-bottom: 1px solid #e5e7eb; padding: 10px 16px; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
        .toolbar a, .toolbar button { font-size: 12px; padding: 6px 12px; border-radius: 8px; border: 1px solid #d1d5db; background: #fff; color: #374151; text-decoration: none; cursor: pointer; }
        .toolbar a.on { background: #0891b2; color: #fff; border-color: #0891b2; }
        .toolbar .print { background: #111827; color: #fff; border-color: #111827; margin-left: auto; }
        .sheet { width: 148mm; min-height: 105mm; margin: 16px auto; background: #fff; border: 1px solid #d1d5db; padding: 8mm; }
        .head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #111827; padding-bottom: 8px; }
        .head .en { text-align: left; }
        .head .ur { text-align: right; }
        .head h1 { font-size: 16px; margin: 0; font-weight: 800; }
        .head .sub { font-size: 10px; color: #6b7280; }
        .courier { text-align: center; }
        .courier img { max-height: 40px; max-width: 120px; object-fit: contain; }
        .courier .name { font-size: 13px; font-weight: 800; letter-spacing: .04em; }
        .boxes { display: flex; gap: 8px; margin-top: 10px; }
        .box { flex: 1; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 10px; }
        .box .label { font-size: 9px; text-transform: uppercase; letter-spacing: .08em; color: #6b7280; font-weight: 700; }
        .box .label-ur { font-size: 12px; color: #374151; }
        .box .name { font-weight: 700; font-size: 13px; }
        .box .meta { font-size: 11px; color: #374151; line-height: 1.5; }
        .codes { display: flex; gap: 8px; margin-top: 10px; align-items: center; }
        .codes .barcode-wrap { flex: 1; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; padding: 6px; }
        .codes .qr { text-align: center; border: 1px solid #d1d5db; border-radius: 6px; padding: 6px; width: 92px; }
        .codes .qr canvas, .codes .qr img { margin: 0 auto; }
        .codes .qr .cap { font-size: 9px; color: #6b7280; margin-top: 2px; }
        .note { margin-top: 10px; border: 1px dashed #9ca3af; border-radius: 6px; padding: 8px 10px; font-size: 11px; background: #fafafa; }
        .note .en { color: #374151; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { margin: 0; border: none; width: auto; }
            @page { size: A5; margin: 8mm; }
        }
    </style>
</head>
@php
    $showEn = $lang !== 'ur';
    $showUr = $lang !== 'en';
    $company = [
        'name'  => setting('site_name', 'AL MUFEED TRADERS'),
        'phone' => setting('site_phone'),
        'addr'  => setting('site_address'),
    ];
    $sender = $from === 'reseller'
        ? ['name' => $order->from_name, 'phone' => $order->from_phone, 'addr' => $order->from_address]
        : ['name' => $company['name'], 'phone' => $company['phone'], 'addr' => $company['addr']];

    $isCod   = ($order->online_payment_status === 'cod') || (\App\Models\PaymentMethod::where('name', $order->payment_method)->value('is_cod'));
    $codAmt  = $isCod ? (float) $order->total : 0;
    $trackNo = $order->tracking_id ?: $order->order_number;
    $postmanEn = setting('dispatch_postman_note', 'Dear postman: if you face any difficulty delivering this parcel, please call the sender — but kindly ensure delivery.');
    $postmanUr = setting('dispatch_postman_note_ur', 'معزز پوسٹ مین: اگر پارسل ڈیلیور کرنے میں کوئی مشکل ہو تو اس نمبر پر رابطہ کریں مگر پارسل کی ڈیلیوری یقینی بنائیں۔');
    $base = url()->current();
    $q = fn($params) => $base . '?' . http_build_query(array_merge(request()->query(), $params));
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
        @if ($from === 'reseller')
            <a href="{{ $q(['logo'=> $withLogo ? 0 : 1]) }}" class="{{ $withLogo?'on':'' }}">+ Company logo</a>
            <a href="{{ $q(['details'=> $withDetails ? 0 : 1]) }}" class="{{ $withDetails?'on':'' }}">+ Company details</a>
        @endif
        <button class="print" onclick="window.print()">🖨 Print</button>
        <a href="{{ route('admin.online-orders.show', $order) }}">← Back</a>
    </div>

    <div class="sheet">
        {{-- Header: English left, Urdu right, courier centre --}}
        <div class="head">
            <div class="en">
                @if ($showEn)
                    <h1>{{ $sender['name'] }}</h1>
                    @if ($withDetails || $from==='company')
                        <div class="sub">{{ $sender['phone'] }}</div>
                    @endif
                @endif
            </div>
            <div class="courier">
                @if ($withLogo && $dispatchMethod?->logo)
                    <img src="{{ asset('storage/'.$dispatchMethod->logo) }}" alt="">
                @elseif ($withLogo && $from==='company')
                    <img src="{{ asset('assets/images/brand/almufeed-traders.png') }}" alt="">
                @endif
                <div class="name">{{ $order->dispatch_method }}</div>
            </div>
            <div class="ur">
                @if ($showUr)
                    <h1 class="urdu">{{ $sender['name'] }}</h1>
                @endif
            </div>
        </div>

        {{-- From / To --}}
        <div class="boxes">
            <div class="box">
                @if ($showEn)<div class="label">From / Sender</div>@endif
                @if ($showUr)<div class="label-ur urdu">بھیجنے والا</div>@endif
                <div class="name">{{ $sender['name'] }}</div>
                <div class="meta">
                    @if ($sender['phone']){{ $sender['phone'] }}<br>@endif
                    @if ($sender['addr']){{ $sender['addr'] }}@endif
                </div>
            </div>
            <div class="box">
                @if ($showEn)<div class="label">To / Recipient</div>@endif
                @if ($showUr)<div class="label-ur urdu">وصول کنندہ</div>@endif
                <div class="name">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</div>
                <div class="meta">
                    {{ $order->shipping_phone }}<br>
                    {{ $order->shipping_address1 }}
                    @if ($order->shipping_address2), {{ $order->shipping_address2 }}@endif<br>
                    {{ collect([$order->shipping_tehsil, $order->shipping_district, $order->shipping_city, $order->shipping_province])->filter()->implode(', ') }}
                    @if ($order->shipping_post_code) — {{ $order->shipping_post_code }}@endif<br>
                    {{ $order->shipping_country }}
                </div>
            </div>
        </div>

        {{-- Codes: tracking barcode + COD QR + weight QR --}}
        <div class="codes">
            <div class="barcode-wrap">
                <svg id="barcode"></svg>
                <div style="font-size:10px;color:#6b7280;">
                    @if ($showEn)Tracking @endif @if ($showUr)<span class="urdu">ٹریکنگ نمبر</span>@endif : <strong>{{ $trackNo }}</strong>
                </div>
            </div>
            @if ($codAmt > 0)
                <div class="qr">
                    <div id="qr-cod"></div>
                    <div class="cap">COD: Rs. {{ number_format($codAmt, 0) }}</div>
                </div>
            @endif
            <div class="qr">
                <div id="qr-weight"></div>
                <div class="cap">{{ rtrim(rtrim(number_format((float)$order->weight, 3), '0'), '.') ?: 0 }} kg</div>
            </div>
        </div>

        {{-- Order line + postman note --}}
        <div style="display:flex;justify-content:space-between;margin-top:8px;font-size:11px;color:#374151;">
            <span>Order <strong>{{ $order->order_number }}</strong> · {{ $order->items->sum('quantity') }} item(s) · {{ now()->format('d M Y') }}</span>
            <span>{{ $isCod ? 'COD' : ucfirst(str_replace('_',' ', $order->payment_method)) }}</span>
        </div>

        <div class="note">
            @if ($showEn)<div class="en">{{ $postmanEn }}</div>@endif
            @if ($showUr && $showEn)<hr style="border:none;border-top:1px solid #e5e7eb;margin:4px 0;">@endif
            @if ($showUr)<div class="urdu" style="font-size:13px;">{{ $postmanUr }}</div>@endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            try { JsBarcode('#barcode', @json((string) $trackNo), { format: 'CODE128', width: 1.6, height: 42, fontSize: 12, margin: 4 }); } catch (e) {}
            @if ($codAmt > 0)
            try { new QRCode(document.getElementById('qr-cod'), { text: 'COD:{{ $codAmt }}:{{ $order->order_number }}', width: 70, height: 70 }); } catch (e) {}
            @endif
            try { new QRCode(document.getElementById('qr-weight'), { text: 'WT:{{ (float)$order->weight }}:{{ $order->order_number }}', width: 70, height: 70 }); } catch (e) {}
        });
    </script>
</body>
</html>
