@extends('layouts.admin')
@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="p-3 sm:p-6">

    <a href="{{ route('admin.online-orders.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-cyan-700 mb-3">
        <i class="fas fa-arrow-left text-xs"></i> Back to all online orders
    </a>

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">
        <div>
            <div class="text-xs uppercase tracking-widest" style="color:#0891b2;">Online order</div>
            <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ $order->order_number }}</h1>
            <p class="text-xs text-gray-500 mt-1">Placed {{ $order->created_at->format('d M Y · h:i A') }}@if(!$order->customer_id) · <span class="font-semibold text-gray-600">GUEST</span>@endif</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ url('/shop/track-order/' . $order->receipt_token) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 rounded-lg text-xs font-semibold">
                <i class="fas fa-eye"></i> Public tracking page
            </a>
            <a href="{{ route('admin.pos.receipt', $order) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 rounded-lg text-xs font-semibold">
                <i class="fas fa-receipt"></i> Receipt
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-emerald-50 text-emerald-800 rounded-lg border border-emerald-200 text-sm"><i class="fas fa-check-circle mr-1"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 text-red-800 rounded-lg border border-red-200 text-sm"><i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-5">

        {{-- LEFT: items + status --}}
        <div class="space-y-5">
            {{-- Status update --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                        <i class="fas fa-truck text-cyan-600"></i> Status &amp; Tracking
                    </h2>
                </div>

                {{-- Timeline --}}
                @php
                    $steps = [
                        'pending'   => ['Pending',   'fa-clock'],
                        'confirmed' => ['Confirmed', 'fa-check'],
                        'shipped'   => ['Shipped',   'fa-truck'],
                        'delivered' => ['Delivered', 'fa-circle-check'],
                    ];
                    $current = $order->status === 'completed' ? 'delivered' : $order->status;
                    $cancelled = $order->status === 'cancelled';
                    $currentIdx = array_search($current, array_keys($steps));
                @endphp
                <div class="p-5 grid grid-cols-4 gap-2 {{ $cancelled ? 'opacity-50' : '' }}">
                    @foreach ($steps as $key => [$label, $icon])
                        @php $idx = array_search($key, array_keys($steps)); $done = !$cancelled && $currentIdx !== false && $idx <= $currentIdx; @endphp
                        <div class="text-center">
                            <div class="w-10 h-10 rounded-full mx-auto flex items-center justify-center text-sm transition"
                                 style="background:{{ $done ? '#0891b2' : '#e5e7eb' }};color:{{ $done ? 'white' : '#9ca3af' }};">
                                <i class="fas {{ $icon }}"></i>
                            </div>
                            <div class="text-[11px] mt-1.5 font-semibold" style="color:{{ $done ? '#0c1f3d' : '#9ca3af' }};">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>
                @if ($cancelled)
                    <div class="px-5 -mt-2 pb-3"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold" style="background:#fee2e2;color:#991b1b;"><i class="fas fa-ban"></i> Cancelled</span></div>
                @endif

                <form method="POST" action="{{ route('admin.online-orders.status', $order) }}" class="border-t border-gray-100 p-5 flex flex-col sm:flex-row sm:items-end gap-3">
                    @csrf @method('PATCH')
                    <div class="sm:w-44 flex-shrink-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 whitespace-nowrap">Update status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                            @foreach (['pending','confirmed','shipped','delivered','cancelled'] as $s)
                                <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 whitespace-nowrap">Tracking ID <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="text" name="tracking_id" value="{{ $order->tracking_id }}" placeholder="e.g. TCS-1234567" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                    </div>
                    <div class="flex-shrink-0">
                        <button class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2 text-white text-sm font-semibold rounded-lg whitespace-nowrap" style="background:linear-gradient(135deg,#0891b2,#0e7490);">
                            <i class="fas fa-check"></i> Update
                        </button>
                    </div>
                </form>
            </div>

            {{-- Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                        <i class="fas fa-box text-cyan-600"></i> Items
                    </h2>
                    <span class="text-xs text-gray-500">{{ $order->items->count() }} item(s)</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach ($order->items as $item)
                        <div class="p-4 flex gap-3">
                            <img src="{{ shop_image($item->product?->image) }}" class="w-14 h-16 rounded-lg object-cover" style="background:#f5f1e8;">
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-800 text-sm">{{ $item->product?->name ?? 'Product' }}</div>
                                <div class="text-[11px] text-gray-500 mt-0.5">Qty {{ (int) $item->quantity }} × Rs. {{ number_format($item->unit_price, 0) }}</div>
                            </div>
                            <div class="text-sm font-bold whitespace-nowrap" style="color:#0c1f3d;">Rs. {{ number_format($item->total_price, 0) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Order notes --}}
            @if ($order->order_notes_customer)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                    <div class="text-xs font-bold uppercase tracking-widest text-amber-700 mb-1">Customer note</div>
                    <p class="text-sm text-gray-800">{{ $order->order_notes_customer }}</p>
                </div>
            @endif
        </div>

        {{-- RIGHT: summary, customer, shipping, payment --}}
        <div class="space-y-4">
            {{-- Summary --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="font-semibold">Rs. {{ number_format($order->subtotal, 0) }}</span></div>
                    @if ($order->coupon_discount > 0)
                        <div class="flex justify-between text-emerald-600"><span>Coupon ({{ $order->coupon_code }})</span><span>-Rs. {{ number_format($order->coupon_discount, 0) }}</span></div>
                    @endif
                    <div class="flex justify-between"><span class="text-gray-500">Delivery</span><span class="font-semibold">Rs. {{ number_format($order->delivery_charges ?? 0, 0) }}</span></div>
                    @if ($order->weight) <div class="flex justify-between text-xs text-gray-500"><span>Weight</span><span>{{ number_format($order->weight, 2) }} kg</span></div> @endif
                </div>
                <hr class="my-3 border-gray-100">
                <div class="flex justify-between items-baseline"><span class="font-bold">Total</span><span class="text-xl font-extrabold" style="color:#0c1f3d;">Rs. {{ number_format($order->total, 0) }}</span></div>
            </div>

            {{-- Payment --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Payment</h3>
                <div class="space-y-1 text-sm">
                    <div><span class="text-gray-500">Method:</span> <span class="font-semibold capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</span></div>
                    <div><span class="text-gray-500">Status:</span> <span class="font-semibold capitalize">{{ str_replace('_', ' ', $order->online_payment_status ?? $order->payment_status) }}</span></div>
                    <div><span class="text-gray-500">Paid:</span> <span class="font-semibold">Rs. {{ number_format($order->paid_amount, 0) }}</span></div>
                    <div><span class="text-gray-500">Balance:</span> <span class="font-bold {{ $order->balance_amount > 0 ? 'text-rose-600' : 'text-emerald-600' }}">Rs. {{ number_format($order->balance_amount, 0) }}</span></div>
                    @if ($order->online_payment_ref)
                        <div class="text-xs text-gray-500 mt-1">Ref: <span class="font-mono">{{ $order->online_payment_ref }}</span></div>
                    @endif
                </div>

                @if ($order->balance_amount > 0 && $order->status !== 'cancelled')
                    <form method="POST" action="{{ route('admin.online-orders.mark-paid', $order) }}" class="mt-4 pt-4 border-t border-gray-100"
                          onsubmit="return confirm('Mark this order as fully paid? Customer khata will be reduced.')">
                        @csrf @method('PATCH')
                        <input type="text" name="payment_ref" placeholder="Payment ref (optional)"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs mb-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <button class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg">
                            <i class="fas fa-check-circle"></i> Mark as Paid
                        </button>
                    </form>
                @endif
            </div>

            {{-- Customer --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Customer</h3>
                @if ($order->customer)
                    <div class="font-bold text-gray-900">{{ $order->customer->name }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ $order->customer->email }}</div>
                    <div class="text-xs text-gray-500">{{ $order->customer->phone }}</div>
                    <a href="{{ route('admin.customers.khata', $order->customer) }}"
                       class="inline-flex items-center gap-1.5 mt-3 text-xs font-semibold" style="color:#0891b2;">
                        <i class="fas fa-book-open"></i> Open khata
                    </a>
                @else
                    <div class="font-bold text-gray-900">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ $order->customer_email }}</div>
                    <div class="text-xs text-gray-500">{{ $order->shipping_phone }}</div>
                    <span class="inline-block mt-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">GUEST CHECKOUT</span>
                @endif
            </div>

            {{-- Shipping --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Shipping</h3>
                <div class="text-sm text-gray-700 leading-relaxed">
                    {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
                    {{ $order->shipping_address1 }}<br>
                    @if ($order->shipping_address2){{ $order->shipping_address2 }}<br>@endif
                    {{ $order->shipping_city }}@if ($order->shipping_post_code), {{ $order->shipping_post_code }}@endif<br>
                    <span class="text-gray-500">{{ $order->shipping_country }}</span>
                </div>
                <div class="text-xs text-gray-500 mt-3"><i class="fas fa-truck"></i> {{ $order->dispatch_method }}</div>
                @if ($order->tracking_id)
                    <div class="text-xs text-gray-700 mt-1"><i class="fas fa-hashtag"></i> {{ $order->tracking_id }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
