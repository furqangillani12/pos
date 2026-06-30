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

        .sheet { width: 148mm; margin: 16px auto; background: #fff; border: 2px solid #111827; }
        .pad { padding: 4mm 5mm; }

        /* ── Header: courier+barcode (left) · payment QR (centre) · company (right) ── */
        .head { display: flex; align-items: stretch; border-bottom: 2px solid #111827; }
        .head > div { padding: 6px 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        /* Order: barcode (left) · logo (centre) · payment QR (right) */
        .head .courier { flex: 1; border-right: 1px solid #d1d5db; text-align: center; }
        .head .brand   { flex: 1.1; border-right: 1px solid #d1d5db; text-align: center; }
        .head .codes   { flex: 1; text-align: center; }
        .courier .dak { font-size: 12px; font-weight: 800; }
        .courier .clogo { max-height: 30px; max-width: 110px; object-fit: contain; margin: 2px auto; display: block; }
        .courier .cname { font-size: 12px; font-weight: 800; letter-spacing: .03em; }
        .courier .barcode { margin-top: 4px; }
        .courier .barcode svg { max-width: 150px; }
        .courier .track { font-size: 11px; color: #111827; }
        .courier .track strong { font-weight: 800; }
        .codes .qr { display: inline-block; }
        .codes .qr canvas, .codes .qr img { margin: 0 auto; }
        .codes .amt { font-size: 13px; font-weight: 800; margin-top: 2px; }
        .codes .ordno { font-size: 12px; font-weight: 700; color: #111827; }
        .brand .blogo { max-height: 40px; max-width: 150px; object-fit: contain; display: inline-block; }
        .brand .bname { font-size: 14px; font-weight: 800; line-height: 1.3; }
        .brand .bsub  { font-size: 11px; color: #374151; }
        .brand .bcontact { font-size: 11px; color: #111827; margin-top: 2px; }

        /* ── Recipient form ── */
        .form { width: 100%; border-collapse: collapse; }
        .form td { border: 1px solid #d1d5db; padding: 5px 8px; font-size: 12px; vertical-align: top; }
        .form .lbl { background: #f3f4f6; font-weight: 800; white-space: nowrap; width: 1%; }
        .form .lbl .en { font-size: 9px; color: #6b7280; font-weight: 700; display: block; }
        .form .val { font-weight: 600; }
        .form .val.big { font-size: 13px; font-weight: 800; }

        .note { border-top: 2px solid #111827; padding: 6px 8px; font-size: 11px; text-align: center; }
        .note .urdu { font-size: 12px; }

        .footer { background: #111827; color: #fff; padding: 6px 8px; font-size: 10px; display: flex; align-items: center; justify-content: center; gap: 12px; flex-wrap: nowrap; }
        .footer span { white-space: nowrap; }

        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { margin: 0; border: 2px solid #111827; width: auto; }
            @page { size: A5; margin: 6mm; }
        }
    </style>
</head>
@php
    $showEn = $lang !== 'ur';
    $showUr = $lang !== 'en';
    // Bilingual label: "اردو / English" in both-mode, else the chosen language.
    $L = function ($en, $ur) use ($showEn, $showUr) {
        if ($showEn && $showUr) return ['ur' => $ur, 'en' => $en];
        return $showUr ? ['ur' => $ur, 'en' => null] : ['ur' => null, 'en' => $en];
    };

    // Per-branch slip (#audio3): the order's own branch supplies the logo +
    // contact, falling back to the global website/brand settings. Editing a
    // branch (name/phone/address/logo) therefore re-skins that branch's slips.
    $branch     = $order->branch;
    $branchLogo = $branch?->logo ? asset('storage/'.$branch->logo) : asset('assets/images/brand/almufeed-traders.png');
    $company = [
        'name'    => $branch?->name ?: setting('site_name', 'AL MUFEED TRADERS'),
        'name_ur' => setting('site_name_ur', 'المفید اسلامی ثقافتی مرکز'),
        'phone'   => $branch?->phone ?: setting('site_phone'),
        'addr'    => $branch?->address ?: setting('site_address'),
        'website' => setting('site_website', 'www.almufeed.com.pk'),
        'whatsapp'=> setting('site_whatsapp'),
    ];
    $isReseller = $from === 'reseller' && $order->from_name;
    // Sender block: reseller's own details, or the company.
    $sender = $isReseller
        ? ['name' => $order->from_name, 'name_ur' => $order->from_name, 'phone' => $order->from_phone, 'addr' => $order->from_address]
        : ['name' => $company['name'], 'name_ur' => $company['name_ur'], 'phone' => $company['phone'], 'addr' => $company['addr']];
    // Company branding shows on company slips; reseller chooses via toggles.
    $showCompanyLogo    = !$isReseller || $withLogo;
    $showCompanyContact = !$isReseller || $withDetails;

    $isCod   = ($order->online_payment_status === 'cod') || (\App\Models\PaymentMethod::where('name', $order->payment_method)->value('is_cod'));
    $codAmt  = (float) $order->total;
    // Barcode prints ONLY once a courier tracking (dak) number is set.
    $hasTracking = (bool) $order->tracking_id;
    // Payment/order QR — scannable, opens the order's public page.
    $payUrl = $order->receipt_token ? route('shop.track.view', $order->receipt_token) : url('/');
    $weightTxt = (rtrim(rtrim(number_format((float)$order->weight, 3), '0'), '.') ?: '0') . ' kg';

    $postmanEn = setting('dispatch_postman_note', 'Dear postman: if delivering this parcel is difficult, please call ' . ($company['phone'] ?: '') . ' — but kindly ensure delivery. Thank you.');
    $postmanUr = setting('dispatch_postman_note_ur', 'محترم ڈاک صاحب! اگر پارسل وصول کنندہ تک پہنچانے میں کوئی مشکل ہو تو ' . ($company['phone'] ?: '') . ' اس نمبر پر رابطہ کریں مگر پارسل کی ڈیلیوری یقینی بنائیں۔ شکریہ');

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
        @if ($isReseller)
            <a href="{{ $q(['logo'=> $withLogo ? 0 : 1]) }}" class="{{ $withLogo?'on':'' }}">+ Company logo</a>
            <a href="{{ $q(['details'=> $withDetails ? 0 : 1]) }}" class="{{ $withDetails?'on':'' }}">+ Company details</a>
        @endif
        @if (!$hasTracking)
            <span style="font-size:11px;color:#b45309;background:#fffbeb;border:1px solid #fde68a;padding:4px 8px;border-radius:6px;">⚠ Add a tracking number to print the barcode</span>
        @endif
        <button class="print" onclick="window.print()">🖨 Print</button>
        <a href="{{ route('admin.online-orders.show', $order) }}">← Back</a>
    </div>

    <div class="sheet">
        {{-- Header --}}
        <div class="head">
            {{-- LEFT: courier logo (at the "Dak Raseed" spot) + tracking barcode --}}
            <div class="courier">
                @if ($showEn)<div class="dak">DISPATCH SLIP</div>@endif
                @if ($showUr)<div class="dak urdu">ڈاک رسید</div>@endif
                @if ($dispatchMethod?->logo)
                    <img class="clogo" src="{{ asset('storage/'.$dispatchMethod->logo) }}" alt="">
                @endif
                <div class="cname">{{ $order->dispatch_method }}</div>
                @if ($hasTracking)
                    <div class="barcode"><svg id="barcode"></svg></div>
                    <div class="track">{{ $showUr ? 'ٹریکنگ' : 'Tracking' }}: <strong>{{ $order->tracking_id }}</strong></div>
                @else
                    <div class="track" style="color:#9ca3af;">{{ $showUr ? 'ٹریکنگ نمبر بعد میں' : 'Tracking added later' }}</div>
                @endif
            </div>

            {{-- CENTRE: company (or reseller) branding — Urdu name only --}}
            <div class="brand">
                @if ($showCompanyLogo && !$isReseller)
                    <img class="blogo" src="{{ $branchLogo }}" alt="" onerror="this.style.display='none'">
                @endif
                <div class="bname urdu">{{ $sender['name_ur'] }}</div>
                @if ($showCompanyContact)
                    <div class="bcontact">
                        @if ($sender['phone'])☎ {{ $sender['phone'] }}@endif
                        @if ($sender['addr'])<br>{{ $sender['addr'] }}@endif
                    </div>
                @endif
            </div>

            {{-- RIGHT: payment QR — kept apart from the barcode so the fast scanner
                 doesn't grab the wrong code. Amount + order number below it. --}}
            <div class="codes">
                <div class="qr"><div id="qr-pay"></div></div>
                <div class="amt">{{ $isCod ? 'COD ' : '' }}Rs. {{ number_format($codAmt, 0) }}</div>
                <div class="ordno">{{ $showUr ? 'آرڈر' : 'Order' }} #{{ $order->order_number }}</div>
            </div>
        </div>

        {{-- Recipient form (RTL labels, like the reference) --}}
        @php
            $rowName    = $L('Name', 'نام');
            $rowAddr    = $L('Address', 'ایڈریس');
            $rowTehsil  = $L('Tehsil', 'تحصیل');
            $rowDist    = $L('District / City', 'ضلع / شہر');
            $rowProv    = $L('Province', 'صوبہ');
            $rowOrder   = $L('Order #', 'آرڈر نمبر');
            $rowTrack   = $L('Tracking #', 'ٹریکنگ نمبر');
            $rowWeight  = $L('Weight', 'وزن');
            $rowPhone   = $L('Phone', 'فون');
        @endphp
        <table class="form">
            <tr>
                <td class="lbl">@if($rowName['ur'])<span class="urdu">{{ $rowName['ur'] }}</span>@endif @if($rowName['en'])<span class="en">{{ $rowName['en'] }}</span>@endif</td>
                <td class="val big">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</td>
                <td class="lbl">@if($rowPhone['ur'])<span class="urdu">{{ $rowPhone['ur'] }}</span>@endif @if($rowPhone['en'])<span class="en">{{ $rowPhone['en'] }}</span>@endif</td>
                <td class="val big">{{ $order->shipping_phone }}</td>
            </tr>
            <tr>
                <td class="lbl">@if($rowAddr['ur'])<span class="urdu">{{ $rowAddr['ur'] }}</span>@endif @if($rowAddr['en'])<span class="en">{{ $rowAddr['en'] }}</span>@endif</td>
                <td class="val" colspan="3">{{ $order->shipping_address1 }}@if ($order->shipping_address2), {{ $order->shipping_address2 }}@endif</td>
            </tr>
            <tr>
                <td class="lbl">@if($rowTehsil['ur'])<span class="urdu">{{ $rowTehsil['ur'] }}</span>@endif @if($rowTehsil['en'])<span class="en">{{ $rowTehsil['en'] }}</span>@endif</td>
                <td class="val">{{ $order->shipping_tehsil ?: '—' }}</td>
                <td class="lbl">@if($rowDist['ur'])<span class="urdu">{{ $rowDist['ur'] }}</span>@endif @if($rowDist['en'])<span class="en">{{ $rowDist['en'] }}</span>@endif</td>
                <td class="val">{{ collect([$order->shipping_district, $order->shipping_city])->filter()->implode(' / ') ?: '—' }}@if ($order->shipping_post_code) — {{ $order->shipping_post_code }}@endif</td>
            </tr>
            <tr>
                <td class="lbl">@if($rowProv['ur'])<span class="urdu">{{ $rowProv['ur'] }}</span>@endif @if($rowProv['en'])<span class="en">{{ $rowProv['en'] }}</span>@endif</td>
                <td class="val">{{ $order->shipping_province ?: '—' }} <span style="color:#6b7280;">({{ $order->shipping_country ?: 'Pakistan' }})</span></td>
                <td class="lbl">@if($rowWeight['ur'])<span class="urdu">{{ $rowWeight['ur'] }}</span>@endif @if($rowWeight['en'])<span class="en">{{ $rowWeight['en'] }}</span>@endif</td>
                <td class="val">{{ $weightTxt }}</td>
            </tr>
            <tr>
                <td class="lbl">@if($rowOrder['ur'])<span class="urdu">{{ $rowOrder['ur'] }}</span>@endif @if($rowOrder['en'])<span class="en">{{ $rowOrder['en'] }}</span>@endif</td>
                <td class="val big">{{ $order->order_number }}</td>
                <td class="lbl">@if($rowTrack['ur'])<span class="urdu">{{ $rowTrack['ur'] }}</span>@endif @if($rowTrack['en'])<span class="en">{{ $rowTrack['en'] }}</span>@endif</td>
                <td class="val big">{{ $order->tracking_id ?: '—' }}</td>
            </tr>
        </table>

        {{-- Postman note — one line --}}
        <div class="note">
            @if ($showUr)<div class="urdu">{{ $postmanUr }}</div>@endif
            @if ($showEn)<div class="en">{{ $postmanEn }}</div>@endif
        </div>

        {{-- Dark footer bar: one number · website · full address (like dispatch.jpeg) --}}
        <div class="footer">
            @if ($company['phone'])<span>☎ {{ $company['phone'] }}</span>@endif
            @if ($company['website'])<span>🌐 {{ $company['website'] }}</span>@endif
            @if ($company['addr'])<span>📍 {{ $company['addr'] }}</span>@endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if ($hasTracking)
            // BARCODE = tracking ID (Code128). displayValue:false — the readable
            // number is printed once on the "Tracking:" line below it.
            try { JsBarcode('#barcode', @json((string) $order->tracking_id), { format: 'CODE128', width: 1.5, height: 40, displayValue: false, margin: 2 }); } catch (e) {}
            @endif
            // QR = order's payment / tracking page URL (scannable, opens the page).
            try { new QRCode(document.getElementById('qr-pay'), { text: @json($payUrl), width: 84, height: 84, correctLevel: QRCode.CorrectLevel.M }); } catch (e) {}
        });
    </script>
</body>
</html>
