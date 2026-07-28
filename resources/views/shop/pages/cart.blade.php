@extends('shop.layouts.app')
@section('title', 'Your cart')

@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-end justify-between mb-8 reveal">
            <div>
                <h1 class="display text-3xl sm:text-4xl font-bold">Your cart</h1>
                <p class="text-gray-500 text-sm mt-2"><span id="cart-count">{{ $items->count() }}</span> item(s)</p>
            </div>
            <a href="{{ route('shop.catalog') }}" class="text-sm text-blue-700 hover:underline hidden sm:inline-flex items-center gap-2">
                <i class="fas fa-arrow-left text-xs"></i> Continue shopping
            </a>
        </div>

        @include('shop.partials.notice', ['class' => 'mb-6 reveal'])

        @if ($items->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center reveal">
                <i class="fas fa-shopping-cart text-5xl text-gray-300 mb-4 block"></i>
                <h2 class="display text-2xl font-bold mb-2">Your cart is empty</h2>
                <p class="text-gray-500 mb-6">Discover something beautiful in our shop.</p>
                <a href="{{ route('shop.catalog') }}" class="btn btn-dark">Start shopping <i class="fas fa-arrow-right text-xs"></i></a>
            </div>
        @else
            <div class="grid lg:grid-cols-[1fr_360px] gap-8" x-data="cartPage()">
                <div class="space-y-3 reveal-stagger">
                    @foreach ($items as $item)
                        <div id="line-{{ $item->id }}" data-unit="{{ (float) $item->unit_price }}"
                             class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-col sm:flex-row gap-4 hover:shadow-md transition">
                            <a href="{{ route('shop.product', $item->product?->slug ?? $item->product?->id) }}" class="flex-shrink-0">
                                <img src="{{ shop_image($item->product?->image) }}" alt="" class="w-full sm:w-28 sm:h-32 object-cover rounded-xl" style="background:#f5f1e8;">
                            </a>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('shop.product', $item->product?->slug ?? $item->product?->id) }}" class="font-bold text-gray-900 hover:text-blue-700 transition block">{{ $item->product?->name ?? 'Product' }}</a>
                                @if ($item->product?->brand)
                                    <div class="text-[10px] uppercase tracking-widest text-gray-400 mt-0.5">{{ $item->product->brand->name }}</div>
                                @endif
                                <div class="text-sm font-bold mt-2" style="color:var(--brand-cyan);">{{ shop_price($item->unit_price) }}</div>
                                <div class="flex flex-wrap items-center gap-3 mt-3">
                                    {{-- Qty updates over AJAX — no page reload; type a number or use ± (client #3) --}}
                                    <div class="flex items-center gap-1 bg-gray-100 rounded-lg overflow-hidden" :class="busy['{{ $item->id }}'] && 'opacity-50 pointer-events-none'">
                                        <button type="button" @click="setQty('{{ route('shop.cart.update', $item) }}', {{ $item->id }}, qtyOf({{ $item->id }}) - 1)" class="px-3 py-1.5 text-gray-600 hover:text-gray-900"><i class="fas fa-minus text-[10px]"></i></button>
                                        <input type="number" min="1" max="9999" id="qty-{{ $item->id }}" value="{{ (int) $item->qty }}"
                                               @change="setQty('{{ route('shop.cart.update', $item) }}', {{ $item->id }}, parseInt($el.value) || 1)"
                                               class="w-12 text-center bg-transparent text-sm font-bold outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none">
                                        <button type="button" @click="setQty('{{ route('shop.cart.update', $item) }}', {{ $item->id }}, qtyOf({{ $item->id }}) + 1)" class="px-3 py-1.5 text-gray-600 hover:text-gray-900"><i class="fas fa-plus text-[10px]"></i></button>
                                    </div>
                                    <button type="button" @click="removeItem('{{ route('shop.cart.remove', $item) }}', {{ $item->id }})" class="text-xs text-red-500 hover:underline"><i class="fas fa-trash text-[10px] mr-1"></i> Remove</button>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500">Line total</div>
                                <div id="lt-{{ $item->id }}" class="text-lg font-extrabold" style="color:var(--brand-navy);">{{ shop_price($item->qty * $item->unit_price) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Summary --}}
                <aside class="lg:sticky lg:top-24 lg:self-start reveal">
                    <div class="bg-white rounded-2xl border border-gray-100 p-6">
                        <h2 class="font-bold text-gray-900 mb-4">Order summary</h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span id="sum-subtotal" class="font-semibold">{{ shop_price($totals['subtotal']) }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Discount</span><span id="sum-discount" class="font-semibold {{ $totals['discount'] > 0 ? 'text-emerald-600' : '' }}">-{{ shop_price($totals['discount']) }}</span></div>
                            @if (($totals['tax'] ?? 0) > 0)
                                <div class="flex justify-between"><span class="text-gray-500">Tax @if ($totals['tax_type'] === 'percent')({{ rtrim(rtrim(number_format($totals['tax_rate'],2),'0'),'.') }}%)@endif</span><span id="sum-tax" class="font-semibold">{{ shop_price($totals['tax']) }}</span></div>
                            @endif
                            <div class="flex justify-between text-xs text-gray-500 italic"><span>Delivery</span><span>calculated at checkout</span></div>
                        </div>
                        <hr class="my-4 border-gray-100">
                        <div class="flex items-baseline justify-between">
                            <span class="font-bold">Total</span>
                            <span id="sum-total" class="text-2xl font-extrabold" style="color:var(--brand-navy);">{{ shop_price($totals['total']) }}</span>
                        </div>

                        <a href="{{ route('shop.checkout') }}" class="btn btn-primary btn-block mt-5">Proceed to checkout <i class="fas fa-arrow-right text-xs"></i></a>

                        {{-- Coupon --}}
                        <div class="mt-5 pt-5 border-t border-gray-100">
                            @if ($coupon)
                                <div class="bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2 flex items-center justify-between">
                                    <div class="text-xs">
                                        <div class="font-bold text-emerald-700">{{ $coupon->code }}</div>
                                        <div class="text-emerald-600">applied</div>
                                    </div>
                                    <form method="POST" action="{{ route('shop.cart.coupon.remove') }}">@csrf @method('DELETE')<button class="text-red-500 text-xs"><i class="fas fa-times"></i></button></form>
                                </div>
                            @else
                                <form method="POST" action="{{ route('shop.cart.coupon') }}" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="code" placeholder="Coupon code" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <button class="btn btn-ghost !py-2 !px-4 !text-xs">Apply</button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-center gap-4 text-xs text-gray-400">
                        <span><i class="fas fa-shield-halved"></i> Secure</span>
                        <span><i class="fas fa-lock"></i> Encrypted</span>
                    </div>
                </aside>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
    function cartPage() {
        return {
            busy: {},
            money(n) { return 'Rs. ' + Math.round(Number(n) || 0).toLocaleString('en-US'); },
            qtyOf(id) { return parseInt(document.getElementById('qty-' + id)?.value) || 1; },
            headers() {
                return { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' };
            },
            applyTotals(d) {
                const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
                set('sum-subtotal', this.money(d.subtotal));
                set('sum-discount', '-' + this.money(d.discount));
                set('sum-tax', this.money(d.tax));
                set('sum-total', this.money(d.total));
                set('cart-count', d.count);
                // Keep the header cart badge + mini-cart in sync (body Alpine state).
                const bodyData = window.Alpine ? window.Alpine.$data(document.body) : null;
                if (bodyData && typeof d.count !== 'undefined') {
                    bodyData.cartCount = d.count;
                    if (typeof bodyData.loadCart === 'function') bodyData.loadCart();
                }
                (d.items || []).forEach(it => {
                    const q = document.getElementById('qty-' + it.id);
                    if (q) q.value = parseInt(it.qty);
                    const lt = document.getElementById('lt-' + it.id);
                    if (lt) lt.textContent = this.money(it.qty * it.unit_price);
                });
            },
            async setQty(url, id, qty) {
                qty = Math.max(1, Math.min(9999, parseInt(qty) || 1));
                document.getElementById('qty-' + id).value = qty;
                this.busy[id] = true;
                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: { ...this.headers(), 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'qty=' + qty,
                    });
                    const d = await res.json();
                    this.applyTotals(d);
                } catch (e) {
                    window.location.reload();
                } finally {
                    this.busy[id] = false;
                }
            },
            async removeItem(url, id) {
                this.busy[id] = true;
                try {
                    const res = await fetch(url, { method: 'DELETE', headers: this.headers() });
                    const d = await res.json();
                    document.getElementById('line-' + id)?.remove();
                    if (!d.items || d.items.length === 0) { window.location.reload(); return; }
                    this.applyTotals(d);
                } catch (e) {
                    window.location.reload();
                }
            },
        };
    }
</script>
@endpush
@endsection
