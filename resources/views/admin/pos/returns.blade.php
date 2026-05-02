@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Returns & Refunds (واپسی)</h1>
            <p class="text-sm text-gray-500 mt-1">Total refunded: <strong class="text-red-600">Rs. {{ number_format($totalAmount, 0) }}</strong></p>
        </div>
        <a href="{{ route('admin.pos.index') }}"
            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 w-fit">
            <i class="fas fa-arrow-left"></i> Back to POS
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-lg shadow px-4 py-3 mb-4 flex flex-wrap gap-3 items-center">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search by order #, customer, refund #..."
            class="border border-gray-300 rounded-md px-3 py-1.5 text-sm flex-1 min-w-[200px] focus:ring-blue-500 focus:border-blue-500">
        <input type="date" name="start_date" value="{{ request('start_date') }}"
            class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-blue-500 focus:border-blue-500">
        <input type="date" name="end_date" value="{{ request('end_date') }}"
            class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-blue-500 focus:border-blue-500">
        <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded-md text-sm hover:bg-blue-700">Filter</button>
        @if(request()->hasAny(['search','start_date','end_date']))
        <a href="{{ route('admin.pos.returns') }}" class="text-sm text-gray-500 hover:text-gray-700">Clear</a>
        @endif
    </form>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Refund #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Items Returned</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reason</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">By</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($refunds as $refund)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">
                            {{ $refund->refund_number ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($refund->order)
                            <a href="{{ route('admin.pos.receipt', $refund->order) }}"
                                class="text-blue-600 hover:underline font-semibold">
                                #{{ $refund->order->order_number }}
                            </a>
                            <div class="text-xs text-gray-400">
                                {{ $refund->order->created_at->format('d M Y') }}
                            </div>
                            @else
                                <span class="text-gray-400">Deleted</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($refund->order?->customer)
                                <div class="font-medium text-gray-800">{{ $refund->order->customer->name }}</div>
                                <div class="text-xs text-gray-400">{{ $refund->order->customer->phone }}</div>
                            @else
                                <span class="text-gray-400 text-xs">Walk-in</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 max-w-[200px]">
                            @if($refund->items)
                                <ul class="space-y-0.5">
                                    @foreach($refund->items as $item)
                                    <li>{{ $item['name'] ?? 'Item' }} × {{ $item['quantity'] }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-400">Full order</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 max-w-[160px]">
                            {{ $refund->reason }}
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-red-600">
                            Rs. {{ number_format($refund->amount, 0) }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                            {{ $refund->created_at->format('d M Y') }}<br>
                            <span class="text-gray-400">{{ $refund->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $refund->user?->name ?? 'Staff' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($refund->order)
                            <a href="{{ route('admin.pos.receipt', $refund->order) }}"
                                class="text-blue-500 hover:text-blue-700 text-xs">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-undo text-3xl mb-3 block opacity-30"></i>
                            No returns found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($refunds->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $refunds->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
