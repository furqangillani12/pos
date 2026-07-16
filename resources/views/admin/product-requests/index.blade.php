@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Product Requests</h1>
            <p class="text-sm text-gray-600 mt-1">Customers ne jo out-of-stock items arrange karne ki request ki.</p>
        </div>
        <div class="flex items-center gap-2 text-sm">
            @foreach (['' => 'All', 'new' => 'New', 'contacted' => 'Contacted', 'arranged' => 'Arranged', 'closed' => 'Closed'] as $val => $label)
                <a href="{{ route('admin.product-requests.index', array_filter(['status' => $val])) }}"
                   class="px-3 py-1.5 rounded-lg border {{ request('status') === $val || (request('status') === null && $val === '') ? 'bg-gray-900 text-white border-gray-900' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg px-4 py-2">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Product</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Customer</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Phone</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Note</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">When</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($requests as $r)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                @if ($r->product)
                                    <a href="{{ route('shop.product', $r->product->slug ?? $r->product->id) }}" target="_blank" class="hover:underline">{{ $r->product_name }}</a>
                                @else
                                    {{ $r->product_name ?? '—' }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $r->name ?: ($r->customer->name ?? 'Guest') }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                @if ($r->phone)<a href="tel:{{ $r->phone }}" class="text-cyan-700 hover:underline">{{ $r->phone }}</a>@endif
                                @if ($r->email)<div class="text-xs text-gray-400">{{ $r->email }}</div>@endif
                            </td>
                            <td class="px-4 py-3 text-gray-600 max-w-xs">{{ $r->note }}</td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $r->created_at->format('d M Y, h:i A') }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.product-requests.status', $r) }}">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                            class="border border-gray-300 rounded-md px-2 py-1 text-xs">
                                        @foreach (['new' => 'New', 'contacted' => 'Contacted', 'arranged' => 'Arranged', 'closed' => 'Closed'] as $val => $label)
                                            <option value="{{ $val }}" {{ $r->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('admin.product-requests.destroy', $r) }}" onsubmit="return confirm('Delete this request?')">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">No requests yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $requests->links() }}</div>
</div>
@endsection
