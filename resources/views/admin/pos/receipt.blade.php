@extends('layouts.admin')

@section('title', 'Receipt #' . $order->order_number)

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 shadow print-content">
        <!-- Receipt Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold">ALMufeed Saqafti Markaz</h1>
            <p class="text-gray-600">www.almufeed.com.pk</p>
            <p class="text-gray-600">Phone: 03007951919</p>
        </div>

        <!-- Order Info -->
        <div class="border-b pb-4 mb-4">
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Receipt #:</span>
                <span>{{ $order->order_number }}</span>
            </div>
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Date:</span>
                <span>{{ $order->created_at?->format('M d, Y h:i A') ?? 'N/A' }}</span>
            </div>
            @if ($order->customer)
                <div class="flex justify-between">
                    <span class="font-semibold">Customer:</span>
                    <span>
                        {{ $order->customer?->name ?? 'Walk-in Customer' }}
                        @if ($order->customer)
                            ({{ ucfirst($order->customer->customer_type) }})
                        @endif
                    </span>
                </div>
            @endif
        </div>

        <!-- Order Items -->
        <div class="border-b pb-4 mb-4">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="border-b-2 border-gray-800">
                        <th class="text-left pb-2 px-1 font-bold text-sm uppercase">Item</th>
                        <th class="text-center pb-2 px-1 font-bold text-sm uppercase w-20">Qty</th>
                        <th class="text-right pb-2 px-1 font-bold text-sm uppercase w-24">Unit Price</th>
                        <th class="text-right pb-2 px-1 font-bold text-sm uppercase w-24">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $index => $item)
                        <tr class="{{ $index % 2 == 0 ? 'bg-gray-50' : '' }} border-b border-gray-100">
                            <td class="py-2 px-1 text-sm">{{ $item->product?->name ?? 'Deleted Product' }}</td>
                            <td class="text-center py-2 px-1 font-medium">{{ $item->quantity ?? 0 }}</td>
                            <td class="text-right py-2 px-1 font-mono text-sm">
                                @if (is_numeric($item->unit_price) && floor($item->unit_price) == $item->unit_price)
                                    {{ number_format($item->unit_price, 0) }}
                                @else
                                    {{ number_format($item->unit_price ?? 0, 2) }}
                                @endif
                            </td>
                            <td class="text-right py-2 px-1 font-mono text-sm font-semibold">
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

        <!-- Order Totals -->
        <div class="mb-6">
            <table class="w-full mb-4">
                <tr class="border-b">
                    <td class="py-1 font-semibold">Subtotal:</td>
                    <td class="py-1 text-right font-mono">
                        @if (is_numeric($order->subtotal) && floor($order->subtotal) == $order->subtotal)
                            {{ number_format($order->subtotal, 0) }}
                        @else
                            {{ number_format($order->subtotal ?? 0, 2) }}
                        @endif
                    </td>
                </tr>
                <tr class="border-b">
                    <td class="py-1 font-semibold">Tax ({{ $order->tax_rate ?? 0 }}%):</td>
                    <td class="py-1 text-right font-mono">
                        @if (is_numeric($order->tax) && floor($order->tax) == $order->tax)
                            {{ number_format($order->tax, 0) }}
                        @else
                            {{ number_format($order->tax ?? 0, 2) }}
                        @endif
                    </td>
                </tr>
                @if (($order->delivery_charges ?? 0) > 0)
                    <tr class="border-b">
                        <td class="py-1 font-semibold">Delivery Charges:</td>
                        <td class="py-1 text-right font-mono">
                            @if (is_numeric($order->delivery_charges) && floor($order->delivery_charges) == $order->delivery_charges)
                                {{ number_format($order->delivery_charges, 0) }}
                            @else
                                {{ number_format($order->delivery_charges ?? 0, 2) }}
                            @endif
                        </td>
                    </tr>
                @endif
                @if (($order->discount ?? 0) > 0)
                    <tr class="border-b">
                        <td class="py-1 font-semibold text-red-600">Discount:</td>
                        <td class="py-1 text-right font-mono text-red-600">-
                            @if (is_numeric($order->discount) && floor($order->discount) == $order->discount)
                                {{ number_format($order->discount, 0) }}
                            @else
                                {{ number_format($order->discount ?? 0, 2) }}
                            @endif
                        </td>
                    </tr>
                @endif
                <tr class="border-t-2 border-gray-800">
                    <td class="py-2 font-bold text-lg">Total Bill:</td>
                    <td class="py-2 text-right font-mono font-bold text-lg">
                        Rs. {{ number_format($order->total ?? 0, 0) }}
                    </td>
                </tr>

                {{-- ── PARTIAL PAYMENT ROWS ── --}}
                <tr class="border-t border-gray-400 bg-green-50">
                    <td class="py-1 font-semibold text-green-700">Amount Paid:</td>
                    <td class="py-1 text-right font-mono font-bold text-green-700">
                        Rs. {{ number_format($order->paid_amount ?? $order->total, 0) }}
                    </td>
                </tr>

                @if (($order->balance_amount ?? 0) > 0)
                    <tr class="bg-red-50">
                        <td class="py-1 font-semibold text-red-600">Balance on Bill:</td>
                        <td class="py-1 text-right font-mono font-bold text-red-600">
                            Rs. {{ number_format($order->balance_amount, 0) }}
                        </td>
                    </tr>
                @endif

                @if (($order->previous_balance ?? 0) > 0)
                    <tr class="bg-orange-50">
                        <td class="py-1 text-sm text-orange-600">Previous Balance:</td>
                        <td class="py-1 text-right font-mono text-sm text-orange-600">
                            Rs. {{ number_format($order->previous_balance, 0) }}
                        </td>
                    </tr>
                @endif

                @php
                    $currentBalance =
                        ($order->previous_balance ?? 0) +
                        ($order->balance_amount ?? $order->total - ($order->paid_amount ?? $order->total));
                @endphp
                @if ($currentBalance > 0)
                    <tr class="bg-yellow-50 border-t-2 border-yellow-400">
                        <td class="py-2 font-bold text-yellow-700">Current Balance Due:</td>
                        <td class="py-2 text-right font-mono font-bold text-yellow-700">
                            Rs. {{ number_format($currentBalance, 0) }}
                        </td>
                    </tr>
                @elseif($currentBalance < 0)
                    <tr class="bg-green-50 border-t-2 border-green-400">
                        <td class="py-2 font-bold text-green-700">Advance Credit:</td>
                        <td class="py-2 text-right font-mono font-bold text-green-700">
                            Rs. {{ number_format(abs($currentBalance), 0) }}
                        </td>
                    </tr>
                @else
                    <tr class="bg-green-50 border-t-2 border-green-400">
                        <td class="py-2 font-bold text-green-700" colspan="2" style="text-align:center">
                            ✅ Account Settled — No Balance Due
                        </td>
                    </tr>
                @endif
                {{-- ── END PARTIAL PAYMENT ROWS ── --}}

            </table>

            <div class="flex justify-between mt-2">
                <span class="font-semibold">Payment Method:</span>
                <span>{{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}</span>
            </div>

            @if ($order->dispatch_method)
                <div class="flex justify-between mt-2">
                    <span class="font-semibold">Dispatch Method:</span>
                    <span>{{ $order->dispatch_method }}</span>
                </div>
            @endif

            @if ($order->tracking_id)
                <div class="flex justify-between mt-1">
                    <span class="font-semibold">Tracking ID:</span>
                    <span>{{ $order->tracking_id }}</span>
                </div>
            @endif

            @if ($order->weight)
                <div class="flex justify-between mt-1">
                    <span class="font-semibold">Parcel Weight:</span>
                    <span>{{ $order->weight }} kg</span>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="text-center text-sm text-gray-500">
            <p>Thank you for your business!</p>
            <p class="mt-1">Items can be returned within 7 days with receipt</p>
            <!-- QR Code for quick access to receipt -->
            <div class="mt-4 p-2 bg-gray-50 rounded inline-block">
                <p class="text-xs mb-1">Scan to view receipt online:</p>
                <div id="qrcode-container" class="mx-auto">
                    <!-- QR code will be generated here -->
                </div>
                <p class="text-xs mt-1 text-blue-600 break-all">{{ $order->receipt_url }}</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons - All in single row -->
    <div class="mt-6 text-center no-print">
        <div class="flex flex-wrap justify-center gap-2">
            <!-- View Receipt Online Button -->
            <a href="{{ $order->receipt_url }}" target="_blank"
                class="bg-cyan-600 text-white px-4 py-2 rounded hover:bg-cyan-700 inline-flex items-center">
                <i class="fas fa-eye mr-2"></i>View Online
            </a>

            <button id="whatsapp-share-btn"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 inline-flex items-center">
                <i class="fab fa-whatsapp mr-2"></i>WhatsApp
            </button>

            <button onclick="window.print()"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 inline-flex items-center">
                <i class="fas fa-print mr-2"></i>Print
            </button>

            <a href="{{ route('admin.pos.receipt.pdf', $order) }}"
                class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 inline-flex items-center">
                <i class="fas fa-download mr-2"></i>PDF
            </a>

            <!-- Copy Receipt Link Button -->
            <button id="copy-link-btn"
                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 inline-flex items-center">
                <i class="fas fa-copy mr-2"></i>Copy Link
            </button>

            <a href="{{ route('admin.pos.index') }}"
                class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300 inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>New Sale
            </a>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .print-content,
            .print-content * {
                visibility: visible;
            }

            .print-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
                background: white;
                max-width: 100% !important;
            }

            .no-print {
                display: none !important;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                padding: 4px 2px;
            }

            .text-right {
                text-align: right;
            }

            .text-center {
                text-align: center;
            }

            .border-b {
                border-bottom: 1px solid #ddd;
            }

            #qrcode-container {
                width: 100px;
                height: 100px;
            }

            #qrcode-container canvas,
            #qrcode-container img {
                width: 100% !important;
                height: 100% !important;
                max-width: 100px;
                max-height: 100px;
            }

            .font-mono {
                font-family: monospace;
            }
        }

        .print-content {
            max-width: 520px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px 4px;
            white-space: nowrap;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .border-b {
            border-bottom: 1px solid #ddd;
        }

        #qrcode-container {
            width: 100px;
            height: 100px;
            margin: 0 auto;
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

        /* Hide the duplicate image that QRCode.js creates */
        #qrcode-container canvas+img {
            display: none !important;
        }

        .font-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        /* Fixed column widths for better mobile display */
        th:nth-child(2),
        td:nth-child(2) {
            width: 60px;
            /* Smaller Qty column */
        }

        th:nth-child(3),
        td:nth-child(3) {
            width: 80px;
            /* Smaller Price column */
        }

        th:nth-child(4),
        td:nth-child(4) {
            width: 80px;
            /* Smaller Total column */
        }

        /* Make first column take remaining space */
        th:nth-child(1),
        td:nth-child(1) {
            width: auto;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Mobile responsive styles */
        @media (max-width: 640px) {
            .print-content {
                padding: 12px;
            }

            th:nth-child(2),
            td:nth-child(2) {
                width: 50px;
                /* Even smaller on mobile */
            }

            th:nth-child(3),
            td:nth-child(3) {
                width: 70px;
                /* Even smaller on mobile */
            }

            th:nth-child(4),
            td:nth-child(4) {
                width: 70px;
                /* Even smaller on mobile */
            }

            th:nth-child(1),
            td:nth-child(1) {
                max-width: 150px;
            }

            th,
            td {
                padding: 6px 3px;
                font-size: 12px;
            }

            .font-mono {
                font-size: 11px;
            }

            /* Keep buttons in single row on mobile */
            .flex-wrap {
                flex-wrap: wrap !important;
            }

            .inline-flex {
                min-width: 100px;
                justify-content: center;
                padding: 8px 12px;
                font-size: 14px;
            }

            .inline-flex i {
                margin-right: 4px;
            }
        }

        @media (max-width: 480px) {

            th:nth-child(2),
            td:nth-child(2) {
                width: 45px;
            }

            th:nth-child(3),
            td:nth-child(3) {
                width: 65px;
            }

            th:nth-child(4),
            td:nth-child(4) {
                width: 65px;
            }

            th:nth-child(1),
            td:nth-child(1) {
                max-width: 120px;
            }

            th,
            td {
                padding: 4px 2px;
                font-size: 11px;
            }

            .font-mono {
                font-size: 10px;
            }

            .inline-flex {
                min-width: 85px;
                padding: 6px 10px;
                font-size: 12px;
            }

            .gap-2 {
                gap: 4px;
            }
        }

        /* Table row styling */
        tr.border-b-2 {
            border-bottom-width: 2px;
        }

        tr.border-t-2 {
            border-top-width: 2px;
        }

        /* Better spacing */
        .px-1 {
            padding-left: 4px;
            padding-right: 4px;
        }

        /* Zebra striping */
        .bg-gray-50 {
            background-color: #f9fafb;
        }

        /* Clear table borders */
        .border-gray-100 {
            border-color: #f3f4f6;
        }

        .border-gray-800 {
            border-color: #1f2937;
        }

        /* Action buttons styling */
        .inline-flex {
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }
    </style>

    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Receipt page loaded');

            // Generate QR Code for receipt URL
            const receiptUrl = "{{ $order->receipt_url }}";
            console.log('Generating QR code for:', receiptUrl);

            if (receiptUrl && receiptUrl !== '') {
                try {
                    // Create a cleaner QR code implementation
                    generateCleanQRCode(receiptUrl);
                } catch (error) {
                    console.error('Error generating QR code:', error);
                    // Fallback: Show URL text
                    document.getElementById('qrcode-container').innerHTML =
                        `<p class="text-xs break-all text-center">${receiptUrl}</p>`;
                }
            } else {
                console.error('Receipt URL is empty');
                document.getElementById('qrcode-container').innerHTML =
                    '<p class="text-xs text-red-500">Receipt URL not available</p>';
            }

            // Simplified WhatsApp Share Button
            const whatsappBtn = document.getElementById('whatsapp-share-btn');
            whatsappBtn.addEventListener('click', function(e) {
                e.preventDefault();

                let customerName = "{{ $order->customer?->name ?? 'Customer' }}";
                let phone = "{{ $order->customer?->phone ?? '' }}";

                if (!phone) {
                    // Ask for phone number
                    phone = prompt(
                        "Enter customer phone number (with country code, e.g., 923001234567):",
                        "92"
                    );

                    if (!phone) {
                        alert("Phone number is required to share receipt on WhatsApp.");
                        return;
                    }
                }

                // Clean the phone number
                phone = phone.replace(/\D/g, '');

                // Ensure it starts with 92 (Pakistan country code)
                if (!phone.startsWith('92')) {
                    // Remove leading zero if present
                    if (phone.startsWith('0')) {
                        phone = '92' + phone.substring(1);
                    } else {
                        phone = '92' + phone;
                    }
                }

                // Create a friendly WhatsApp message
                let message = `*AlMufeed Saqafti Markaz - Receipt*\n\n`;
                message += `Dear ${customerName},\n\n`;
                message += `Thank you for shopping with us!\n\n`;
                message += `*Receipt #*: {{ $order->order_number }}\n`;
                message += `*Date*: {{ $order->created_at?->format('d M, Y h:i A') }}\n`;
                message += `*Total Amount*: Rs. {{ number_format($order->total ?? 0, 2) }}\n`;
                message +=
                    `*Payment Method*: {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}\n\n`;

                @if ($order->dispatch_method)
                    message += `*Dispatch Method*: {{ $order->dispatch_method }}\n`;
                @endif

                @if ($order->tracking_id)
                    message += `*Tracking ID*: {{ $order->tracking_id }}\n`;
                    @if ($order->weight)
                        message += `*Weight*: {{ $order->weight }} kg\n`;
                    @endif
                @endif

                message += `\n📄 *View Your Receipt Online:*\n${receiptUrl}\n\n`;
                message += `You can view, print, or download your receipt from this link.\n\n`;
                message += `*Contact Us:*\n📞 0300-7951919\n🌐 www.almufeed.com.pk\n\n`;
                message += `We appreciate your purchase!`;

                // Create WhatsApp URL (using the simpler API link)
                const whatsappURL =
                    `https://api.whatsapp.com/send?phone=${phone}&text=${encodeURIComponent(message)}`;

                console.log('Opening WhatsApp URL:', whatsappURL);

                // Open in a new tab
                window.open(whatsappURL, '_blank');
            });

            // Copy Link Button
            const copyLinkBtn = document.getElementById('copy-link-btn');
            copyLinkBtn.addEventListener('click', async function() {
                const receiptUrl = "{{ $order->receipt_url }}";

                try {
                    await navigator.clipboard.writeText(receiptUrl);

                    // Show success message
                    const originalText = copyLinkBtn.innerHTML;
                    copyLinkBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
                    copyLinkBtn.classList.remove('bg-indigo-600');
                    copyLinkBtn.classList.add('bg-green-600');

                    setTimeout(() => {
                        copyLinkBtn.innerHTML = originalText;
                        copyLinkBtn.classList.remove('bg-green-600');
                        copyLinkBtn.classList.add('bg-indigo-600');
                    }, 2000);
                } catch (err) {
                    console.error('Failed to copy: ', err);

                    // Fallback for older browsers
                    const tempInput = document.createElement('input');
                    tempInput.value = receiptUrl;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempInput);

                    // Show success message for fallback
                    const originalText = copyLinkBtn.innerHTML;
                    copyLinkBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
                    copyLinkBtn.classList.remove('bg-indigo-600');
                    copyLinkBtn.classList.add('bg-green-600');

                    setTimeout(() => {
                        copyLinkBtn.innerHTML = originalText;
                        copyLinkBtn.classList.remove('bg-green-600');
                        copyLinkBtn.classList.add('bg-indigo-600');
                    }, 2000);
                }
            });
        });

        // Clean QR Code generation that handles the duplicate elements
        function generateCleanQRCode(text) {
            const container = document.getElementById('qrcode-container');

            // Clear container first
            container.innerHTML = '';

            // Create a wrapper div for better control
            const wrapper = document.createElement('div');
            wrapper.id = 'qrcode-wrapper';
            wrapper.style.width = '100px';
            wrapper.style.height = '100px';
            wrapper.style.position = 'relative';

            container.appendChild(wrapper);

            // Generate QR code
            const qrcode = new QRCode(wrapper, {
                text: text,
                width: 100,
                height: 100,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });

            // After QR code is generated, remove the duplicate image
            setTimeout(() => {
                // Find and remove the duplicate img element that QRCode.js creates
                const images = wrapper.getElementsByTagName('img');
                if (images.length > 0) {
                    // Keep only the first img element, remove any extras
                    for (let i = 1; i < images.length; i++) {
                        images[i].remove();
                    }
                }

                // Also hide any duplicate canvas elements
                const canvases = wrapper.getElementsByTagName('canvas');
                if (canvases.length > 1) {
                    // Keep only the first canvas
                    for (let i = 1; i < canvases.length; i++) {
                        canvases[i].style.display = 'none';
                    }
                }
            }, 100);
        }

        // Alternative: Use a different approach without QRCode.js library
        function generateQRCodeAlternative(text) {
            const container = document.getElementById('qrcode-container');
            container.innerHTML = '';

            // Create a canvas element
            const canvas = document.createElement('canvas');
            canvas.width = 100;
            canvas.height = 100;
            canvas.style.display = 'block';
            canvas.style.margin = '0 auto';

            container.appendChild(canvas);

            const ctx = canvas.getContext('2d');

            // Simple QR code generation (basic implementation)
            // For production, you might want to use a proper QR code library
            // or generate it on the server side

            // Fill with white background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, 100, 100);

            // Draw a simple pattern (just as a fallback)
            ctx.fillStyle = '#000000';
            ctx.font = '10px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('QR Code', 50, 50);
            ctx.font = '8px Arial';
            ctx.fillText('Not available', 50, 65);

            // Add the URL below as text
            const urlText = document.createElement('p');
            urlText.className = 'text-xs mt-2 break-all text-center';
            urlText.textContent = text;
            container.appendChild(urlText);
        }
    </script>
@endsection
