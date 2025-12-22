@extends('layouts.admin')

@section('title', 'Receipt #'.$order->order_number)

@section('content')
    <div class="max-w-md mx-auto bg-white p-6 shadow print-content">
        <!-- Receipt Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold">ALMufeed Saqafti Markaz</h1>
            <p class="text-gray-600">www.almufeed.com.pk</p>
            <p class="text-gray-600">Phone: 03007951919</p>
{{--            <p class="text-gray-600">Tax ID: 123456789</p>--}}
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
            @if($order->customer)
                <div class="flex justify-between">
                    <span class="font-semibold">Customer:</span>
                    <span>{{ $order->customer?->name ?? 'N/A' }}</span>
                </div>
            @endif
        </div>

        <!-- Order Items -->
        <div class="border-b pb-4 mb-4">
            <table class="w-full">
                <thead>
                <tr class="border-b">
                    <th class="text-left pb-1">Item</th>
                    <th class="text-right pb-1">Qty</th>
                    <th class="text-right pb-1">Price</th>
                    <th class="text-right pb-1">Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td class="py-1">{{ $item->product?->name ?? 'Deleted Product' }}</td>
                        <td class="text-right py-1">{{ $item->quantity ?? 0 }}</td>
                        <td class="text-right py-1"> {{ number_format($item->unit_price ?? 0, 2) }}</td>
                        <td class="text-right py-1"> {{ number_format($item->total_price ?? 0, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Order Totals -->
        <!-- Order Totals -->
        <div class="mb-6">
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Subtotal:</span>
                <span> {{ number_format($order->subtotal ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Tax ({{ $order->tax_rate ?? 0 }}%):</span>
                <span> {{ number_format($order->tax ?? 0, 2) }}</span>
            </div>
            @if(($order->delivery_charges ?? 0) > 0)
                <div class="flex justify-between mb-1">
                    <span class="font-semibold">Delivery Charges:</span>
                    <span> {{ number_format($order->delivery_charges ?? 0, 2) }}</span>
                </div>
            @endif
            @if(($order->discount ?? 0) > 0)
                <div class="flex justify-between mb-1">
                    <span class="font-semibold">Discount:</span>
                    <span> - {{ number_format($order->discount ?? 0, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between text-lg font-bold mt-2">
                <span>Total:</span>
                <span> {{ number_format($order->total ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between mt-2">
                <span class="font-semibold">Payment Method:</span>
                <span>{{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}</span>
            </div>

            @if($order->dispatch_method)
                <div class="flex justify-between mt-2">
                    <span class="font-semibold">Dispatch Method:</span>
                    <span>{{ $order->dispatch_method }}</span>
                </div>
            @endif

            @if($order->tracking_id)
                <div class="flex justify-between mt-1">
                    <span class="font-semibold">Tracking ID:</span>
                    <span>{{ $order->tracking_id }}</span>
                </div>
            @endif

            @if($order->tracking_id)
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
{{--            <p class="mt-4">Powered by Almufeed Saqafti Markaz</p>--}}
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 text-center no-print">
        <button id="whatsapp-share-btn" class="bg-green-600 text-white px-4 py-2 rounded">
            Share on WhatsApp
        </button>
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded">
            Print Receipt
        </button>
        <a href="{{ route('admin.pos.index') }}" class="ml-2 bg-gray-200 px-4 py-2 rounded">
            New Sale
        </a>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            .print-content, .print-content * { visibility: visible; }
            .print-content { position: absolute; left: 0; top: 0; width: 100%; padding: 20px; background: white; }
            .no-print { display: none !important; }
        }
        .print-content { max-width: 400px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px 0; }
        .text-right { text-align: right; }
        .border-b { border-bottom: 1px solid #ddd; }
    </style>

    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const whatsappBtn = document.getElementById('whatsapp-share-btn');

                whatsappBtn.addEventListener('click', function() {
                    let customerName = "{{ $order->customer?->name ?? 'Customer' }}";
                    let phone = "{{ $order->customer?->phone ?? '' }}";

                    if (!phone) {
                        phone = prompt("Enter customer phone number (with country code, e.g., 92300xxxxxxx):");
                        if (!phone) {
                            alert("Phone number is required to share receipt on WhatsApp.");
                            return;
                        }
                    }

                    phone = phone.replace(/\D/g,'');

                    // ✅ Use your public receipt download route
                    let receiptUrl = "{{ route('admin.pos.receipt.pdf', $order) }}";

                    let message = `Dear ${customerName},\n\nThank you for shopping with *AlMufeed Saqafti Markaz*! \n\nYour receipt #{{ $order->order_number }} is ready.\n\n*Total Amount:* Rs. {{ number_format($order->total ?? 0, 2) }}\n Download Receipt: ${receiptUrl}\n\nIf you have any questions, feel free to contact us at  0300-7951919 (WhatsApp available).\n\nWe appreciate your purchase and look forward to serving you again!`;


                    let whatsappURL = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
                    window.open(whatsappURL, '_blank');
                });
            });

        </script>
    @endsection

@endsection
