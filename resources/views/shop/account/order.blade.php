@extends('shop.layouts.app')
@section('title', 'Order ' . $order->order_number)
@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-4xl mx-auto px-4 reveal">
        <a href="{{ route('shop.account.orders') }}" class="text-xs text-gray-500 hover:text-cyan-700 inline-flex items-center gap-2 mb-4"><i class="fas fa-arrow-left"></i> Back to my orders</a>

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
            <div>
                <span class="text-xs uppercase tracking-widest" style="color:var(--brand-cyan);">Order</span>
                <h1 class="display text-3xl font-bold mt-1">{{ $order->order_number }}</h1>
                <p class="text-gray-500 text-sm mt-1">Placed {{ $order->created_at->format('d M Y · h:i A') }}</p>
            </div>
            <span class="chip capitalize self-start" style="background:#ecfeff;color:var(--brand-cyan);font-size:13px;padding:6px 14px;">{{ str_replace('_', ' ', $order->status) }}</span>
        </div>

        {{-- Status timeline --}}
        @php
            $steps = [
                'pending'   => ['Pending',   'fa-clock'],
                'confirmed' => ['Confirmed', 'fa-check'],
                'shipped'   => ['Shipped',   'fa-truck'],
                'delivered' => ['Delivered', 'fa-box-circle-check'],
            ];
            $current = $order->status === 'completed' ? 'delivered' : $order->status;
            $currentIdx = array_search($current, array_keys($steps));
        @endphp

        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 reveal">
            <div class="grid grid-cols-4 gap-2">
                @foreach ($steps as $key => [$label, $icon])
                    @php $idx = array_search($key, array_keys($steps)); $done = $currentIdx !== false && $idx <= $currentIdx; @endphp
                    <div class="text-center">
                        <div class="w-10 h-10 rounded-full mx-auto flex items-center justify-center text-sm transition"
                             style="background:{{ $done ? 'var(--brand-cyan)' : '#e5e7eb' }};color:{{ $done ? 'white' : '#9ca3af' }};">
                            <i class="fas {{ $icon }}"></i>
                        </div>
                        <div class="text-[11px] mt-1.5 font-semibold" style="color:{{ $done ? 'var(--brand-navy)' : '#9ca3af' }};">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Items + summary --}}
        <div class="grid lg:grid-cols-[1fr_320px] gap-6 reveal">
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50"><h2 class="font-bold text-gray-800">Items</h2></div>
                <div class="divide-y divide-gray-100">
                    @foreach ($order->items as $item)
                        <div class="p-4 flex gap-3">
                            <img src="{{ shop_image($item->product?->image) }}" class="w-16 h-20 rounded-lg object-cover" style="background:#f5f1e8;">
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-800 truncate">{{ $item->product?->name ?? 'Product' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">Qty {{ (int) $item->quantity }} × {{ shop_price($item->unit_price) }}</div>
                            </div>
                            <div class="text-sm font-bold whitespace-nowrap" style="color:var(--brand-navy);">{{ shop_price($item->total_price) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-800 mb-3">Summary</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="font-semibold">{{ shop_price($order->subtotal) }}</span></div>
                        @if ($order->coupon_discount > 0)
                            <div class="flex justify-between text-emerald-600"><span>Coupon</span><span>-{{ shop_price($order->coupon_discount) }}</span></div>
                        @endif
                        <div class="flex justify-between"><span class="text-gray-500">Delivery</span><span class="font-semibold">{{ shop_price($order->delivery_charges ?? 0) }}</span></div>
                    </div>
                    <hr class="my-3 border-gray-100">
                    <div class="flex justify-between items-baseline"><span class="font-bold">Total</span><span class="text-lg font-extrabold" style="color:var(--brand-navy);">{{ shop_price($order->total) }}</span></div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-800 mb-3">Shipping</h3>
                    <div class="text-sm text-gray-700 leading-relaxed">
                        {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
                        {{ $order->shipping_address1 }}<br>
                        @if ($order->shipping_address2){{ $order->shipping_address2 }}<br>@endif
                        {{ $order->shipping_city }}@if ($order->shipping_post_code), {{ $order->shipping_post_code }}@endif<br>
                        <span class="text-gray-500">{{ $order->shipping_phone }}</span>
                    </div>
                    <div class="text-xs text-gray-500 mt-3"><i class="fas fa-truck"></i> {{ $order->dispatch_method }}</div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-800 mb-3">Payment</h3>
                    <div class="text-sm capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</div>
                    <div class="text-xs text-gray-500 mt-1 capitalize">{{ str_replace('_', ' ', $order->payment_status) }}</div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
