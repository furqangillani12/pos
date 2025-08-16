@extends('layouts.admin')

@section('title', 'Receipt #'.$order->order_number)

@section('content')
    <div class="max-w-md mx-auto bg-white p-6 shadow print-content">
        <!-- Receipt Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold">{{ config('app.name') }}</h1>
            <p class="text-gray-600">123 Business Street, City</p>
            <p class="text-gray-600">Phone: (123) 456-7890</p>
            <p class="text-gray-600">Tax ID: 123456789</p>
        </div>

        <!-- Order Info -->
        <div class="border-b pb-4 mb-4">
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Receipt #:</span>
                <span>{{ $order->order_number }}</span>
            </div>
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Date:</span>
                <span>{{ $order->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Cashier:</span>
                <span>{{ $order->user->name }}</span>
            </div>
            @if($order->customer)
                <div class="flex justify-between">
                    <span class="font-semibold">Customer:</span>
                    <span>{{ $order->customer->name }}</span>
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
                        <td class="py-1">{{ $item->product->name }}</td>
                        <td class="text-right py-1">{{ $item->quantity }}</td>
                        <td class="text-right py-1">Rs. {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right py-1">Rs. {{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Order Totals -->
        <div class="mb-6">
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Subtotal:</span>
                <span>Rs. {{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Tax ({{ $order->tax_rate }}%):</span>
                <span>Rs. {{ number_format($order->tax, 2) }}</span>
            </div>
            @if($order->discount > 0)
                <div class="flex justify-between mb-1">
                    <span class="font-semibold">Discount:</span>
                    <span>-Rs. {{ number_format($order->discount, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between text-lg font-bold mt-2">
                <span>Total:</span>
                <span>Rs. {{ number_format($order->total, 2) }}</span>
            </div>
            <div class="flex justify-between mt-2">
                <span class="font-semibold">Payment Method:</span>
                <span>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-sm text-gray-500">
            <p>Thank you for your business!</p>
            <p class="mt-1">Items can be returned within 7 days with receipt</p>
            <p class="mt-4">Powered by {{ config('app.name') }}</p>
        </div>
    </div>

    <!-- Action Buttons (Hidden when printing) -->
    <div class="mt-6 text-center no-print">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded">
            Print Receipt
        </button>
{{--        <a href="{{ route('admin.pos.receipt.download', $order) }}" class="ml-2 bg-green-600 text-white px-4 py-2 rounded">--}}
{{--            Download PDF--}}
{{--        </a>--}}
        <a href="{{ route('admin.pos.index') }}" class="ml-2 bg-gray-200 px-4 py-2 rounded">
            New Sale
        </a>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .print-content, .print-content * {
                visibility: visible;
            }
            .print-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
                background: white;
            }
            .no-print {
                display: none !important;
            }
        }

        /* Ensure consistent styling for PDF */
        .print-content {
            max-width: 400px;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 4px 0;
        }
        .text-right {
            text-align: right;
        }
        .border-b {
            border-bottom: 1px solid #ddd;
        }
    </style>
@endsection
