<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Add html2canvas library for image conversion -->
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 0;
                margin: 0;
                font-size: 12px;
            }

            .receipt-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
            }

            .product-table {
                font-size: 11px !important;
            }

            .product-table th,
            .product-table td {
                padding: 6px 4px !important;
            }
        }

        /* Main container styling */
        body {
            background: #f5f7fa;
            padding: 20px;
        }

        .main-card {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            body {
                padding: 10px !important;
                font-size: 13px;
            }

            .main-card {
                padding: 15px;
                border-radius: 8px;
            }

            /* Remove horizontal scroll from product table */
            .product-table-container {
                overflow-x: hidden !important;
                -webkit-overflow-scrolling: auto !important;
            }

            .product-table {
                min-width: auto !important;
                width: 100% !important;
                font-size: 11px !important;
            }

            .product-table th,
            .product-table td {
                padding: 6px 4px !important;
                font-size: 11px !important;
            }

            /* Adjust product name column for mobile */
            .product-name-cell {
                max-width: 120px !important;
                word-wrap: break-word !important;
                overflow: hidden !important;
            }

            .receipt-number {
                font-size: 14px !important;
                word-break: break-all;
            }

            .company-header {
                padding: 10px 15px !important;
            }

            /* Larger logo on mobile */
            .logo {
                max-height: 70px !important;
            }

            .info-grid {
                grid-template-columns: 1fr !important;
                gap: 10px !important;
                padding: 0 10px !important;
            }

            .info-box {
                padding: 10px !important;
            }

            .info-row {
                margin-bottom: 4px !important;
                font-size: 12px !important;
            }

            .info-label {
                min-width: 100px !important;
                font-size: 12px !important;
            }

            .summary-box {
                margin: 10px !important;
                padding: 12px !important;
            }

            .summary-row {
                font-size: 12px !important;
                margin-bottom: 6px !important;
            }

            .grand-total {
                font-size: 14px !important;
            }

            /* Keep buttons in single row on mobile */
            .action-buttons {
                flex-direction: row !important;
                gap: 10px !important;
                flex-wrap: wrap;
                justify-content: center;
            }

            .action-btn {
                padding: 10px 15px !important;
                font-size: 13px !important;
                flex: 1;
                min-width: 140px;
                justify-content: center;
            }
        }

        /* Very small phones */
        @media (max-width: 480px) {
            body {
                font-size: 12px;
            }

            .main-card {
                padding: 12px;
            }

            .product-table {
                font-size: 10px !important;
            }

            .product-table th,
            .product-table td {
                padding: 5px 3px !important;
                font-size: 10px !important;
            }

            .logo {
                max-height: 100px !important;
            }

            .action-buttons {
                gap: 8px !important;
            }

            .action-btn {
                padding: 8px 12px !important;
                font-size: 12px !important;
                min-width: 120px;
            }

            /* Even smaller fonts for many items */
            .product-name-cell {
                max-width: 100px !important;
                font-size: 10px !important;
            }
        }

        /* Extra small phones */
        @media (max-width: 360px) {
            .product-table {
                font-size: 9px !important;
            }

            .product-table th,
            .product-table td {
                padding: 4px 2px !important;
                font-size: 9px !important;
            }

            .product-name-cell {
                max-width: 80px !important;
                font-size: 9px !important;
            }

            .action-btn {
                padding: 6px 10px !important;
                font-size: 11px !important;
                min-width: 100px !important;
            }
        }

        /* Company header */
        .company-header {
            text-align: center;
            padding: 15px 0;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 20px;
        }

        /* Larger logo on desktop */
        .logo {
            max-height: 90px;
            display: block;
            margin: 0 auto 15px auto;
            width: auto;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .company-address {
            font-size: 12px;
            color: #666;
            line-height: 1.4;
        }

        /* Section titles without green tag */
        .section-title {
            background: #f8f9fa;
            padding: 10px 15px;
            margin: 20px 0 15px 0;
            font-weight: bold;
            color: #333;
            border-radius: 5px;
            border-left: none;
            font-size: 15px;
        }

        /* Information grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 0 15px;
        }

        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .info-label {
            color: #666;
            min-width: 120px;
        }

        .info-value {
            color: #333;
            font-weight: 500;
            text-align: right;
            word-break: break-word;
        }

        /* Summary box */
        .summary-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .grand-total {
            border-top: 2px solid #dee2e6;
            margin-top: 15px;
            padding-top: 15px;
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        /* Notes section */
        .notes-section {
            padding: 0 15px 20px 15px;
            font-size: 12px;
            color: #666;
        }

        /* Action buttons - smaller and centered */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            flex-wrap: wrap;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 150px;
        }

        .print-btn {
            background: #4CAF50;
            color: white;
        }

        .download-btn {
            background: #2196F3;
            color: white;
        }

        .image-btn {
            background: #9C27B0;
            color: white;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .print-btn:hover {
            background: #45a049;
        }

        .download-btn:hover {
            background: #0b7dda;
        }

        .image-btn:hover {
            background: #7B1FA2;
        }

        /* Product table styling - no horizontal scroll */
        .product-table-container {
            padding: 0 15px;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Ensures columns fit within container */
        }

        /* Column width adjustments */
        .product-table th:nth-child(1),
        .product-table td:nth-child(1) {
            width: 8%;
            /* # column */
            text-align: center;
        }

        .product-table th:nth-child(2),
        .product-table td:nth-child(2) {
            width: 45%;
            /* Product Description column */
            text-align: left;
            word-wrap: break-word;
        }

        .product-table th:nth-child(3),
        .product-table td:nth-child(3) {
            width: 10%;
            /* Qty column */
            text-align: center;
        }

        .product-table th:nth-child(4),
        .product-table td:nth-child(4) {
            width: 18%;
            /* Unit Price column */
            text-align: right;
        }

        .product-table th:nth-child(5),
        .product-table td:nth-child(5) {
            width: 19%;
            /* Total column */
            text-align: right;
        }

        /* Print specific styles */
        @media print {
            body {
                background: white !important;
            }

            .main-card {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                border-radius: 0 !important;
            }

            .company-header {
                border-bottom: 2px solid #000 !important;
                padding: 10px 0 !important;
            }

            .logo {
                max-height: 70px !important;
            }

            .section-title {
                background: none !important;
                border: 1px solid #ddd !important;
            }

            .info-box {
                background: none !important;
                border: 1px solid #ddd !important;
            }

            .summary-box {
                background: none !important;
                border: 1px solid #ddd !important;
            }

            .product-table {
                border-collapse: collapse;
                width: 100%;
            }

            .product-table th,
            .product-table td {
                border: 1px solid #ddd;
            }
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }

        .loading-spinner {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }

        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #2196F3;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <!-- Loading overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <div id="loadingText">Converting to image...</div>
        </div>
    </div>

    <div class="main-card" id="receiptContent">
        <!-- Company Header -->
        <div class="company-header">
            <div>
                @if (file_exists(public_path('assets/images/mufeed.png')))
                    <img src="{{ asset('assets/images/mufeed.png') }}" alt="AlMufeed Saqafti Markaz" class="logo">
                @endif
                <div class="company-name">AlMufeed Saqafti Markaz</div>
                <div class="company-address">
                    {{-- AL MUFEED Traders PanjGirain Tehsil DaryaKhan District Bhakkar<br> --}}
                    Phone: 0300-7951919 | Email: Amt7212@gmail.com
                </div>
            </div>
        </div>

        <!-- Receipt Title -->
        <div style="text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #4CAF50;">
            <h1 style="font-size: 24px; font-weight: bold; color: #333;">SALES INVOICE</h1>
            <p style="font-size: 14px; color: #666; margin-top: 5px;">Receipt #{{ $order->order_number }}</p>
        </div>

        <!-- Order Information -->
        <div class="section-title">ORDER INFORMATION</div>
        <div class="info-grid">
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Date & Time:</span>
                    <span class="info-value">{{ $order->created_at->format('d M, Y h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-value capitalize">{{ $order->payment_method }}</span>
                </div>
                @if ($order->dispatch_method)
                    <div class="info-row">
                        <span class="info-label">Dispatch Method:</span>
                        <span class="info-value">{{ $order->dispatch_method }}</span>
                    </div>
                @endif
                @if ($order->tracking_id)
                    <div class="info-row">
                        <span class="info-label">Tracking ID:</span>
                        <span class="info-value">{{ $order->tracking_id }}</span>
                    </div>
                @endif
            </div>

            <!-- Customer Information -->
            <div class="info-box">
                @if ($order->customer)
                    <div class="info-row">
                        <span class="info-label">Customer Name:</span>
                        <span class="info-value">{{ $order->customer->name }}</span>
                    </div>
                    @if ($order->customer->phone)
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $order->customer->phone }}</span>
                        </div>
                    @endif
                    @if ($order->customer->email)
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $order->customer->email }}</span>
                        </div>
                    @endif
                @else
                    <div class="info-row">
                        <span class="info-label">Customer:</span>
                        <span class="info-value">Walk-in Customer</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Purchased Items -->
        <div class="section-title">PURCHASED ITEMS</div>
        <div class="product-table-container">
            <table class="product-table">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="border: 1px solid #dee2e6; padding: 10px; font-weight: bold;">#</th>
                        <th style="border: 1px solid #dee2e6; padding: 10px; font-weight: bold;">Product Description
                        </th>
                        <th style="border: 1px solid #dee2e6; padding: 10px; font-weight: bold;">Qty</th>
                        <th style="border: 1px solid #dee2e6; padding: 10px; font-weight: bold;">Unit Price</th>
                        <th style="border: 1px solid #dee2e6; padding: 10px; font-weight: bold;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $index => $item)
                        <tr>
                            <td style="border: 1px solid #dee2e6; padding: 10px; text-align: center;">
                                {{ $index + 1 }}</td>
                            <td style="border: 1px solid #dee2e6; padding: 10px; word-wrap: break-word;"
                                class="product-name-cell">
                                <div style="font-weight: 500;">{{ $item->product->name }}</div>
                                @if ($item->product->sku)
                                    <div style="color: #666;">SKU: {{ $item->product->sku }}</div>
                                @endif
                            </td>
                            <td style="border: 1px solid #dee2e6; padding: 10px; text-align: center;">
                                {{ $item->quantity }}</td>
                            <td style="border: 1px solid #dee2e6; padding: 10px; text-align: right;">
                                @if ($item->unit_price == floor($item->unit_price))
                                    {{ number_format($item->unit_price, 0) }}
                                @else
                                    {{ number_format($item->unit_price, 2) }}
                                @endif
                            </td>
                            <td style="border: 1px solid #dee2e6; padding: 10px; text-align: right; font-weight: 500;">
                                @if ($item->total_price == floor($item->total_price))
                                    {{ number_format($item->total_price, 0) }}
                                @else
                                    {{ number_format($item->total_price, 2) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Payment Summary -->
        <div class="section-title">PAYMENT SUMMARY</div>
        <div class="summary-box">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>Rs. {{ number_format($order->subtotal, 2) }}</span>
            </div>

            @if ($order->tax_rate > 0 && $order->tax > 0)
                <div class="summary-row">
                    <span>Tax ({{ $order->tax_rate }}%):</span>
                    <span>Rs. {{ number_format($order->tax, 2) }}</span>
                </div>
            @endif

            @if ($order->discount > 0)
                <div class="summary-row" style="color: #4CAF50;">
                    <span>Discount:</span>
                    <span>- Rs. {{ number_format($order->discount, 2) }}</span>
                </div>
            @endif

            @if ($order->delivery_charges > 0)
                <div class="summary-row">
                    <span>Delivery Charges:</span>
                    <span>Rs. {{ number_format($order->delivery_charges, 2) }}</span>
                </div>
            @endif

            @if ($order->weight > 0)
                <div class="summary-row">
                    <span>Total Weight:</span>
                    <span>{{ number_format($order->weight, 2) }} kg</span>
                </div>
            @endif

            <div class="summary-row grand-total">
                <span>GRAND TOTAL:</span>
                <span style="color: #4CAF50; font-size: 18px;">Rs. {{ number_format($order->total, 2) }}</span>
            </div>

            @if ($order->payment_method === 'cash')
                <div class="summary-row" style="margin-top: 10px; font-size: 13px; color: #666;">
                    <span>Payment Status:</span>
                    <span>Paid in Cash</span>
                </div>
            @endif
        </div>

        <!-- Notes -->
        @if ($order->notes)
            <div class="section-title">ORDER NOTES</div>
            <div class="notes-section">
                {{ $order->notes }}
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="action-buttons no-print">
            <button onclick="window.print()" class="action-btn print-btn">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <a href="{{ route('public.receipt.download', $order->receipt_token) }}" class="action-btn download-btn"
                style="text-decoration: none;">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            {{-- <button onclick="downloadAsPNG()" class="action-btn image-btn">
                <i class="fas fa-image"></i> Download PNG
            </button> --}}
            <button onclick="downloadAsJPG()" class="action-btn image-btn">
                <i class="fas fa-image"></i> Download JPG
            </button>
        </div>
    </div>

    <script>
        // Print shortcut
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });

        // Function to adjust table for long data
        function adjustTableForLongData() {
            const table = document.querySelector('.product-table');
            const container = document.querySelector('.product-table-container');
            const rows = table.querySelectorAll('tbody tr');

            if (window.innerWidth < 768 && rows.length > 5) {
                // If many rows on mobile, make fonts even smaller
                table.style.fontSize = '10px';
                const cells = table.querySelectorAll('td, th');
                cells.forEach(cell => {
                    cell.style.fontSize = '10px';
                    cell.style.padding = '4px 3px';
                });

                // Adjust product name column width
                const productCells = table.querySelectorAll('td:nth-child(2)');
                productCells.forEach(cell => {
                    cell.style.maxWidth = '100px';
                    cell.style.fontSize = '10px';
                });
            }
        }

        // Adjust on load and resize
        window.addEventListener('load', adjustTableForLongData);
        window.addEventListener('resize', adjustTableForLongData);

        // Image download functions
        function showLoading(message = 'Converting to image...') {
            document.getElementById('loadingText').textContent = message;
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function downloadAsPNG() {
            showLoading('Converting to PNG...');

            // Temporarily hide the action buttons for clean screenshot
            const actionButtons = document.querySelector('.action-buttons');
            const originalDisplay = actionButtons.style.display;
            actionButtons.style.display = 'none';

            // Add a small delay to ensure DOM updates
            setTimeout(() => {
                html2canvas(document.getElementById('receiptContent'), {
                    scale: 2, // Higher quality
                    useCORS: true, // For loading external images
                    backgroundColor: '#ffffff',
                    logging: false,
                    allowTaint: true
                }).then(canvas => {
                    // Restore action buttons
                    actionButtons.style.display = originalDisplay;

                    // Convert canvas to PNG
                    const link = document.createElement('a');
                    link.download =
                        `Receipt-{{ $order->order_number }}-${new Date().toISOString().split('T')[0]}.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();

                    hideLoading();
                }).catch(error => {
                    console.error('Error converting to PNG:', error);
                    alert('Failed to convert to PNG. Please try again.');
                    actionButtons.style.display = originalDisplay;
                    hideLoading();
                });
            }, 100);
        }

        function downloadAsJPG() {
            showLoading('Converting to JPG...');

            // Temporarily hide the action buttons for clean screenshot
            const actionButtons = document.querySelector('.action-buttons');
            const originalDisplay = actionButtons.style.display;
            actionButtons.style.display = 'none';

            // Add a small delay to ensure DOM updates
            setTimeout(() => {
                html2canvas(document.getElementById('receiptContent'), {
                    scale: 2, // Higher quality
                    useCORS: true, // For loading external images
                    backgroundColor: '#ffffff',
                    logging: false,
                    allowTaint: true
                }).then(canvas => {
                    // Restore action buttons
                    actionButtons.style.display = originalDisplay;

                    // Convert canvas to JPG with quality
                    const link = document.createElement('a');
                    link.download =
                        `Receipt-{{ $order->order_number }}-${new Date().toISOString().split('T')[0]}.jpg`;

                    // Use toDataURL with quality parameter for JPG
                    link.href = canvas.toDataURL('image/jpeg', 0.95); // 95% quality
                    link.click();

                    hideLoading();
                }).catch(error => {
                    console.error('Error converting to JPG:', error);
                    alert('Failed to convert to JPG. Please try again.');
                    actionButtons.style.display = originalDisplay;
                    hideLoading();
                });
            }, 100);
        }

        // Optional: Add keyboard shortcuts for image downloads
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.shiftKey && e.key === 'P') {
                e.preventDefault();
                downloadAsPNG();
            }
            if (e.ctrlKey && e.shiftKey && e.key === 'J') {
                e.preventDefault();
                downloadAsJPG();
            }
        });
    </script>
</body>

</html>
