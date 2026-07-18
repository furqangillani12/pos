@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Coupons</h1>
            <p class="text-sm text-gray-600 mt-1">Discount codes jo customers cart par apply karte hain.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="px-4 py-2 bg-cyan-700 hover:bg-cyan-800 text-white text-sm font-semibold rounded-lg">
            <i class="fas fa-plus"></i> New Coupon
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg px-4 py-2">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Code</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Discount</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Min order</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Used / Limit</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Validity</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($coupons as $c)
                        <tr>
                            <td class="px-4 py-3 font-mono font-semibold text-gray-900">{{ $c->code }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $c->type === 'percent' ? rtrim(rtrim(number_format($c->value, 2), '0'), '.') . '%' : 'Rs ' . number_format($c->value, 0) }}
                                @if ($c->max_discount)<span class="text-xs text-gray-400">(max Rs {{ number_format($c->max_discount, 0) }})</span>@endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $c->min_order_amount ? 'Rs ' . number_format($c->min_order_amount, 0) : '—' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $c->used_count ?? 0 }} / {{ $c->usage_limit ?: '∞' }}</td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap text-xs">
                                {{ $c->starts_on ? $c->starts_on->format('d M Y') : '—' }} → {{ $c->expires_on ? $c->expires_on->format('d M Y') : '∞' }}
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.coupons.toggle', $c) }}">
                                    @csrf @method('PATCH')
                                    <button class="px-2 py-1 rounded-full text-xs font-semibold {{ $c->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $c->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.coupons.edit', $c) }}" class="text-cyan-700 hover:underline mr-3"><i class="fas fa-pen"></i></a>
                                <form method="POST" action="{{ route('admin.coupons.destroy', $c) }}" class="inline" onsubmit="return confirm('Delete this coupon?')">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">No coupons yet. <a href="{{ route('admin.coupons.create') }}" class="text-cyan-700 hover:underline">Create one</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $coupons->links() }}</div>
</div>
@endsection
