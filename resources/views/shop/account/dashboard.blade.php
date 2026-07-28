@extends('shop.layouts.app')
@section('title', 'My Account')
@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Welcome --}}
        <div class="rounded-3xl p-8 sm:p-10 mb-8 text-white relative overflow-hidden reveal" style="background:linear-gradient(135deg,var(--brand-navy),var(--brand-cyan));">
            <div class="hero-pattern absolute inset-0"></div>
            <div class="relative">
                <div class="text-xs uppercase tracking-widest mb-2" style="color:var(--gold);">My account</div>
                <h1 class="display text-3xl sm:text-4xl font-bold">{{ $customer->name }}</h1>
                <p class="text-blue-100/80 mt-2 text-sm">Manage your orders, profile, and addresses from here.</p>
                {{-- Order / wishlist / khata pills removed — the same numbers already
                     show in the cards below (client feedback), so no duplication. --}}
            </div>
        </div>

        {{-- Balance + order status summary (client #1a / #1d) --}}
        @php
            $cid = $customer->id;
            $statusCounts = \App\Models\Order::where('customer_id', $cid)
                ->selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status');
            $cTotal     = (int) $statusCounts->sum();
            $cDispatch  = (int) (($statusCounts['dispatched'] ?? 0) + ($statusCounts['shipped'] ?? 0));
            $cDelivered = (int) ($statusCounts['delivered'] ?? 0);
            $cCancelled = (int) ($statusCounts['cancelled'] ?? 0);
            $cReturned  = (int) ($statusCounts['returned'] ?? 0);
            $bal = (float) ($customer->current_balance ?? 0);
        @endphp

        {{-- Account balance: pending (red) vs credit (green) --}}
        <div class="rounded-2xl border p-5 mb-4 reveal
            {{ $bal > 0 ? 'border-red-200 bg-red-50' : ($bal < 0 ? 'border-emerald-200 bg-emerald-50' : 'border-gray-100 bg-white') }}">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    @if ($bal > 0)
                        <div class="text-xs uppercase tracking-widest font-bold text-red-600">Pending amount</div>
                        <div class="text-3xl font-extrabold text-red-700 mt-1">{{ shop_price($bal) }}</div>
                        <p class="text-xs text-red-600/80 mt-1">Aap par itni raqam baqi hai.</p>
                    @elseif ($bal < 0)
                        <div class="text-xs uppercase tracking-widest font-bold text-emerald-600">Credit / advance</div>
                        <div class="text-3xl font-extrabold text-emerald-700 mt-1">{{ shop_price(abs($bal)) }}</div>
                        <p class="text-xs text-emerald-600/80 mt-1">Aap ka itna balance humare paas jama hai.</p>
                    @else
                        <div class="text-xs uppercase tracking-widest font-bold text-gray-500">Account balance</div>
                        <div class="text-3xl font-extrabold text-gray-800 mt-1">{{ shop_price(0) }}</div>
                        <p class="text-xs text-gray-500 mt-1">Sab settled — koi baqaya nahi.</p>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    @if ($bal > 0)
                        <a href="{{ route('shop.account.pay') }}" class="btn btn-primary !py-2"><i class="fas fa-wallet"></i> Pay now</a>
                    @elseif ($bal < 0)
                        <a href="{{ route('shop.account.withdraw') }}" class="btn btn-primary !py-2" style="background:#059669;"><i class="fas fa-money-bill-wave"></i> Withdraw</a>
                    @endif
                    <a href="{{ route('shop.account.statement') }}" class="btn btn-ghost !py-2"><i class="fas fa-file-invoice"></i> Statement</a>
                </div>
            </div>
        </div>

        {{-- Order status counts --}}
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-4 reveal">
            @foreach ([
                ['Total', $cTotal, '#0b3a30', 'fa-receipt'],
                ['Dispatched', $cDispatch, '#1d4ed8', 'fa-truck'],
                ['Delivered', $cDelivered, '#047857', 'fa-circle-check'],
                ['Cancelled', $cCancelled, '#991b1b', 'fa-ban'],
                ['Returned', $cReturned, '#b45309', 'fa-rotate-left'],
            ] as [$label, $count, $color, $icon])
                <a href="{{ route('shop.account.orders') }}" class="bg-white border border-gray-100 rounded-2xl p-4 text-center hover:shadow-md transition">
                    <i class="fas {{ $icon }} mb-1" style="color:{{ $color }};"></i>
                    <div class="text-2xl font-extrabold" style="color:{{ $color }};">{{ $count }}</div>
                    <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wide">{{ $label }}</div>
                </a>
            @endforeach
        </div>

        {{-- Reward points banner --}}
        <a href="{{ route('shop.account.points') }}" class="block rounded-2xl p-5 mb-4 text-white reveal" style="background:linear-gradient(135deg,var(--brand-navy),var(--brand-cyan));">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs uppercase tracking-widest opacity-80">Reward points</div>
                    <div class="text-3xl font-extrabold mt-1">🏆 {{ number_format($customer->loyalty_points ?? 0) }}</div>
                </div>
                <span class="text-sm font-semibold opacity-90">View history <i class="fas fa-arrow-right text-xs"></i></span>
            </div>
        </a>

        {{-- Quick links --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 reveal-stagger">
            @foreach ([
                ['route'=>'shop.account.orders',   'icon'=>'fa-receipt',           'title'=>'My orders', 'desc'=>'View order history'],
                ['route'=>'shop.account.history',  'icon'=>'fa-clock-rotate-left', 'title'=>'Payments',  'desc'=>'Pay/withdraw + receipts'],
                ['route'=>'shop.account.points',  'icon'=>'fa-star',         'title'=>'Points',     'desc'=>'Reward points'],
                ['route'=>'shop.wishlist',        'icon'=>'fa-heart',        'title'=>'Wishlist',   'desc'=>'Saved items'],
                ['route'=>'shop.account.profile', 'icon'=>'fa-user',         'title'=>'Profile',    'desc'=>'Edit your details'],
            ] as $card)
                <a href="{{ route($card['route']) }}" class="bg-white border border-gray-100 rounded-2xl p-5 hover:shadow-xl hover:-translate-y-1 transition group">
                    <span class="w-12 h-12 rounded-xl flex items-center justify-center mb-3" style="background:linear-gradient(135deg,#e8f1fb,#d6ecfa);color:var(--brand-navy);">
                        <i class="fas {{ $card['icon'] }} text-lg"></i>
                    </span>
                    <div class="font-bold text-gray-900">{{ $card['title'] }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ $card['desc'] }}</div>
                </a>
            @endforeach
        </div>

        {{-- Recent orders --}}
        <div class="mt-10 reveal">
            <div class="flex items-end justify-between mb-4">
                <h2 class="display text-2xl font-bold">Recent orders</h2>
                <a href="{{ route('shop.account.orders') }}" class="text-sm font-semibold" style="color:var(--brand-cyan);">View all <i class="fas fa-arrow-right text-xs"></i></a>
            </div>

            @if ($recentOrders->isEmpty())
                <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center text-gray-500">
                    <i class="fas fa-receipt text-4xl text-gray-300 mb-3 block"></i>
                    No orders yet. <a href="{{ route('shop.catalog') }}" class="font-semibold underline" style="color:var(--brand-cyan);">Start shopping</a>.
                </div>
            @else
                <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Order</th>
                                <th class="px-4 py-3 text-left">Date</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($recentOrders as $o)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono text-xs">{{ $o->order_number }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $o->created_at->format('d M Y') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="chip capitalize" style="background:#e8f1fb;color:var(--brand-cyan);">{{ str_replace('_', ' ', $o->status) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold">{{ shop_price($o->total) }}</td>
                                    <td class="px-4 py-3 text-right"><a href="{{ route('shop.account.order', $o) }}" class="text-blue-700 hover:underline text-xs">View</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
@push('styles')<style>.hero-pattern{background-image:linear-gradient(rgba(255,255,255,.05) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.05) 1px,transparent 1px);background-size:48px 48px;}</style>@endpush
