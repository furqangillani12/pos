@extends('layouts.admin')

@section('title', 'Receipt #'.$order->order_number)

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 shadow">
        <h1 class="text-2xl font-bold mb-4">Receipt #{{ $order->order_number }}</h1>

        <div class="mb-4">
            <p><strong>Customer:</strong> {{ $order->customer->name }} ({{ $order->customer->email }})</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
        </div>

        <table class="min-w-full divide-y divide-gray-200 mb-4">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($order->items as $item)
                <tr>
                    <td class="px-6 py-4">{{ $item->product->name }}</td>
                    <td class="px-6 py-4">{{ $item->quantity }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="text-right mb-4">
            <p><strong>Tax:</strong> Rs{{ number_format($order->tax, 2) }}</p>
            <p><strong>Total:</strong> Rs{{ number_format($order->total, 2) }}</p>
        </div>

        <div class="text-right">
            <a href="{{ route('admin.pos.receipt', $order) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Reprint
            </a>
        </div>
    </div>
@endsection
