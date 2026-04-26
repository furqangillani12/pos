@extends('shop.layouts.app')
@section('title', 'Checkout')

@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 reveal">
            <h1 class="display text-3xl sm:text-4xl font-bold">Checkout</h1>
            <p class="text-gray-500 text-sm mt-2">A few details and your order is on the way.</p>
        </div>

        @if ($isGuest)
            <div class="bg-cyan-50 border border-cyan-200 rounded-2xl p-4 mb-6 flex flex-col sm:flex-row items-center justify-between gap-3 reveal">
                <div class="flex items-center gap-3">
                    <i class="fas fa-circle-info text-lg" style="color:var(--brand-cyan);"></i>
                    <div>
                        <div class="font-semibold text-gray-800 text-sm">Checking out as a guest</div>
                        <div class="text-xs text-gray-600">Track your order any time at <strong>/track-order</strong> using the order number we'll email you. Or <a href="{{ route('shop.login') }}" class="font-semibold underline" style="color:var(--brand-cyan);">sign in</a> for faster checkout next time.</div>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('shop.checkout.place') }}" x-data="{ method: 'cod', dispatch: '{{ $dispatchMethods->first()?->name }}' }" class="grid lg:grid-cols-[1fr_360px] gap-8">
            @csrf

            <div class="space-y-6 reveal">
                @if ($isGuest)
                    <div class="bg-white rounded-2xl border border-gray-100 p-6">
                        <h2 class="font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-envelope" style="color:var(--brand-cyan);"></i> Contact info</h2>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Email *</label>
                            <input type="email" name="guest_email" required value="{{ old('guest_email') }}" placeholder="you@example.com"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                            <p class="text-[11px] text-gray-500 mt-1">We'll send your order confirmation here.</p>
                        </div>
                    </div>
                @endif

                {{-- Shipping address --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-truck" style="color:var(--brand-cyan);"></i> Shipping address</h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">First name *</label>
                            <input type="text" name="shipping_first_name" required value="{{ old('shipping_first_name', $customer ? (explode(' ', $customer->name)[0] ?? '') : '') }}" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Last name</label>
                            <input type="text" name="shipping_last_name" value="{{ old('shipping_last_name', $customer ? \Str::after($customer->name, ' ') : '') }}" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Phone *</label>
                            <input type="text" name="shipping_phone" required value="{{ old('shipping_phone', $customer?->phone) }}" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">City *</label>
                            <input type="text" name="shipping_city" required value="{{ old('shipping_city') }}" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Address line 1 *</label>
                            <input type="text" name="shipping_address1" required value="{{ old('shipping_address1', $customer?->address) }}" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Address line 2</label>
                            <input type="text" name="shipping_address2" value="{{ old('shipping_address2') }}" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Post code</label>
                            <input type="text" name="shipping_post_code" value="{{ old('shipping_post_code') }}" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        </div>
                    </div>
                </div>

                {{-- Dispatch method --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-box" style="color:var(--brand-cyan);"></i> Delivery method</h2>
                    <div class="space-y-2">
                        @foreach ($dispatchMethods as $dm)
                            <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition"
                                   :class="dispatch === '{{ $dm->name }}' ? 'border-cyan-500 bg-cyan-50/40' : 'border-gray-100 hover:border-gray-200'">
                                <input type="radio" name="dispatch_method" value="{{ $dm->name }}" x-model="dispatch" class="text-cyan-600">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-800 text-sm">{{ $dm->name }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Payment method --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-credit-card" style="color:var(--brand-cyan);"></i> Payment method</h2>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition"
                               :class="method === 'cod' ? 'border-cyan-500 bg-cyan-50/40' : 'border-gray-100 hover:border-gray-200'">
                            <input type="radio" name="payment_method" value="cod" x-model="method" class="mt-1 text-cyan-600">
                            <div>
                                <div class="font-semibold text-gray-800">Cash on Delivery</div>
                                <div class="text-[11px] text-gray-500 mt-0.5">Pay when you receive your order</div>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition"
                               :class="method === 'bank_transfer' ? 'border-cyan-500 bg-cyan-50/40' : 'border-gray-100 hover:border-gray-200'">
                            <input type="radio" name="payment_method" value="bank_transfer" x-model="method" class="mt-1 text-cyan-600">
                            <div>
                                <div class="font-semibold text-gray-800">Bank Transfer</div>
                                <div class="text-[11px] text-gray-500 mt-0.5">We'll share account details after order</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <label class="text-xs font-semibold text-gray-600 mb-1 block">Order notes (optional)</label>
                    <textarea name="order_notes_customer" rows="2" placeholder="Anything you'd like us to know..." class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"></textarea>
                </div>
            </div>

            {{-- Summary --}}
            <aside class="lg:sticky lg:top-24 lg:self-start reveal">
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-bold text-gray-900 mb-4">Order summary</h2>
                    <div class="space-y-3 max-h-72 overflow-y-auto pr-2 mb-4">
                        @foreach ($items as $i)
                            <div class="flex gap-3">
                                <img src="{{ shop_image($i->product?->image) }}" class="w-14 h-16 object-cover rounded-lg" style="background:#f5f1e8;">
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-semibold truncate">{{ $i->product?->name }}</div>
                                    <div class="text-[11px] text-gray-500">Qty {{ (int) $i->qty }}</div>
                                </div>
                                <div class="text-sm font-bold whitespace-nowrap" style="color:var(--brand-navy);">{{ shop_price($i->qty * $i->unit_price) }}</div>
                            </div>
                        @endforeach
                    </div>
                    <hr class="my-4 border-gray-100">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="font-semibold">{{ shop_price($totals['subtotal']) }}</span></div>
                        @if ($totals['discount'] > 0)
                            <div class="flex justify-between text-emerald-600"><span>Coupon ({{ $coupon->code }})</span><span>-{{ shop_price($totals['discount']) }}</span></div>
                        @endif
                        <div class="flex justify-between text-xs text-gray-500 italic"><span>Delivery</span><span>by weight ({{ number_format($weight, 2) }} kg)</span></div>
                    </div>
                    <hr class="my-4 border-gray-100">
                    <div class="flex items-baseline justify-between">
                        <span class="font-bold">Total</span>
                        <span class="text-2xl font-extrabold" style="color:var(--brand-navy);">{{ shop_price($totals['total']) }}<span class="text-xs font-normal text-gray-500"> + delivery</span></span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mt-5"><i class="fas fa-lock"></i> Place order</button>

                    <p class="text-[11px] text-gray-400 text-center mt-3">By placing your order you agree to our <a href="{{ route('shop.terms') }}" class="underline">terms</a>.</p>
                </div>
            </aside>
        </form>
    </div>
</section>
@endsection
