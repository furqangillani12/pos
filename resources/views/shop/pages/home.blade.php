@extends('shop.layouts.app')

@section('title', 'Home')
@section('description', 'AL MUFEED TRADERS — quality and affordability you can trust. Shop online from our trusted retail in PanjGirain, Bhakkar.')

@section('content')

{{-- ═════════════════ HERO ═════════════════ --}}
<section class="hero py-20 sm:py-28">
    <div class="hero-pattern absolute inset-0"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 items-center">
        <div class="reveal-stagger">
            <span class="chip" style="background:rgba(251,191,36,.15);color:#fde68a;border:1px solid rgba(251,191,36,.3);">
                <span style="width:6px;height:6px;background:#fbbf24;border-radius:9999px;display:inline-block;animation:pulse 2s infinite;"></span>
                Pakistan's most trusted retail
            </span>
            <h1 class="display text-5xl sm:text-6xl lg:text-7xl font-bold leading-tight mt-5">
                Quality &amp;<br>
                <span style="color:var(--gold);">affordability</span><br>
                in every box.
            </h1>
            <p class="text-base sm:text-lg text-sky-100/80 max-w-md mt-6">
                Discover hand-picked products from <strong>AL MUFEED TRADERS</strong> — now online with same-day fulfilment from our shop in PanjGirain.
            </p>
            <div class="flex flex-wrap gap-3 mt-8">
                <a href="{{ route('shop.catalog') }}" class="btn btn-primary">Shop now <i class="fas fa-arrow-right text-xs"></i></a>
                <a href="#features" class="btn btn-ghost text-white border-white/20 hover:bg-white/10">Learn more</a>
            </div>
            <div class="grid grid-cols-3 gap-4 sm:gap-8 mt-10 max-w-md pt-6 border-t border-white/15">
                <div><div class="text-2xl font-extrabold" style="color:var(--gold);">100%</div><div class="text-[11px] text-sky-100/70 mt-1">Authentic</div></div>
                <div><div class="text-2xl font-extrabold" style="color:var(--gold);">{{ \App\Models\Product::onWebsite()->count() }}+</div><div class="text-[11px] text-sky-100/70 mt-1">Products</div></div>
                <div><div class="text-2xl font-extrabold" style="color:var(--gold);">24/7</div><div class="text-[11px] text-sky-100/70 mt-1">Support</div></div>
            </div>
        </div>

        @if ($heroBanners->isNotEmpty())
            @php $hero = $heroBanners->take(6); @endphp
            <div class="reveal"
                 x-data="{
                    active: 0,
                    count: {{ $hero->count() }},
                    timer: null,
                    start() { if (this.count > 1) { this.stop(); this.timer = setInterval(() => this.next(), 5000); } },
                    stop()  { if (this.timer) { clearInterval(this.timer); this.timer = null; } },
                    next()  { this.active = (this.active + 1) % this.count; },
                    prev()  { this.active = (this.active - 1 + this.count) % this.count; },
                    go(i)   { this.active = i; this.start(); }
                 }"
                 x-init="start()">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl aspect-[4/5]"
                     @mouseenter="stop()" @mouseleave="start()">

                    @foreach ($hero as $i => $b)
                        <a href="{{ $b->cta_url ?: route('shop.catalog') }}"
                           class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                           :class="active === {{ $i }} ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'"
                           @if ($i !== 0) style="opacity:0" @endif>
                            <img src="{{ shop_image($b->image) }}" alt="{{ $b->title }}"
                                 class="w-full h-full object-cover">
                            @if ($b->title || $b->subtitle || $b->cta_text)
                                <div class="absolute inset-x-0 bottom-0 p-5 sm:p-7 text-white"
                                     style="background:linear-gradient(to top,rgba(0,0,0,.72),rgba(0,0,0,.28) 45%,transparent);">
                                    @if ($b->subtitle)
                                        <div class="text-[11px] uppercase tracking-wide font-semibold mb-1" style="color:var(--gold,#fbbf24);">{{ $b->subtitle }}</div>
                                    @endif
                                    @if ($b->title)
                                        <div class="text-xl sm:text-2xl font-bold leading-snug">{{ $b->title }}</div>
                                    @endif
                                    @if ($b->cta_text)
                                        <span class="inline-flex items-center gap-1.5 mt-3 text-sm font-semibold bg-white/90 text-gray-900 px-4 py-2 rounded-full">{{ $b->cta_text }} <i class="fas fa-arrow-right text-[10px]"></i></span>
                                    @endif
                                </div>
                            @endif
                        </a>
                    @endforeach

                    @if ($hero->count() > 1)
                        <button type="button" @click.prevent="prev()" aria-label="Previous slide"
                            class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-white/80 hover:bg-white text-gray-800 flex items-center justify-center shadow">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </button>
                        <button type="button" @click.prevent="next()" aria-label="Next slide"
                            class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-white/80 hover:bg-white text-gray-800 flex items-center justify-center shadow">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </button>
                        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex gap-2">
                            @foreach ($hero as $i => $b)
                                <button type="button" @click.prevent="go({{ $i }})" aria-label="Go to slide {{ $i + 1 }}"
                                    class="h-2.5 rounded-full transition-all"
                                    :class="active === {{ $i }} ? 'bg-white w-6' : 'bg-white/50 hover:bg-white/80 w-2.5'"></button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="reveal relative">
                <div class="rounded-3xl overflow-hidden aspect-[4/5] shadow-2xl"
                     style="background:linear-gradient(135deg,#fbbf24 0%,#d97706 50%,#0c1f3d 100%);">
                </div>
                <div class="absolute -bottom-6 -left-6 bg-white text-gray-900 rounded-2xl p-4 shadow-xl flex items-center gap-3 max-w-xs">
                    <span class="w-12 h-12 rounded-xl flex items-center justify-center"
                          style="background:linear-gradient(135deg,var(--brand-navy),var(--brand-cyan));color:#fbbf24;">
                        <i class="fas fa-truck"></i>
                    </span>
                    <div>
                        <div class="font-bold text-sm">Free delivery</div>
                        <div class="text-[11px] text-gray-500">on orders above Rs. 5,000</div>
                    </div>
                </div>
                <div class="absolute -top-4 -right-4 bg-white text-gray-900 rounded-2xl p-3 shadow-xl flex items-center gap-2 hidden sm:flex">
                    <i class="fas fa-shield-halved" style="color:var(--brand-cyan);"></i>
                    <div class="text-xs font-semibold">100% authentic</div>
                </div>
            </div>
        @endif
    </div>
</section>

{{-- ═════════════════ TRUST STRIP ═════════════════ --}}
<section id="features" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-2 sm:grid-cols-4 gap-6 reveal-stagger">
        @foreach ([
            ['fa-truck',          'Fast delivery',   'Same-day from local branch'],
            ['fa-shield-halved',  'Secure shopping', '100% authentic products'],
            ['fa-rotate-left',    'Easy returns',    '7-day return policy'],
            ['fa-headset',        'Real support',    'Talk to a real person'],
        ] as [$icon, $title, $sub])
            <div class="flex items-start gap-3">
                <span class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                      style="background:linear-gradient(135deg,#ecfeff,#fef3c7);color:var(--brand-navy);">
                    <i class="fas {{ $icon }}"></i>
                </span>
                <div>
                    <div class="font-bold text-gray-900 text-sm">{{ $title }}</div>
                    <div class="text-[12px] text-gray-500 mt-0.5">{{ $sub }}</div>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- ═════════════════ FEATURED CATEGORIES ═════════════════ --}}
@if ($featuredCategories->isNotEmpty())
<section class="py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-10 reveal">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest" style="color:var(--brand-cyan);">Browse</span>
                <h2 class="display text-3xl sm:text-4xl font-bold mt-2">Shop by category</h2>
            </div>
            <a href="{{ route('shop.catalog') }}" class="text-sm font-semibold inline-flex items-center gap-2 hover:gap-3 transition-all" style="color:var(--brand-navy);">
                View all <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 reveal-stagger">
            @foreach ($featuredCategories as $cat)
                <a href="{{ route('shop.category', $cat->slug) }}"
                   class="group relative rounded-2xl overflow-hidden bg-gray-100 hover:shadow-xl transition" style="aspect-ratio:1;">
                    <img src="{{ shop_image($cat->photo) }}" alt="{{ $cat->name }}" loading="lazy"
                         class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    <div class="absolute inset-0" style="background:linear-gradient(180deg,transparent 50%,rgba(12,31,61,.7));"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                        <div class="font-bold text-base">{{ $cat->name }}</div>
                        <div class="text-[11px] opacity-80 inline-flex items-center gap-1 mt-1 group-hover:gap-2 transition-all">
                            Shop now <i class="fas fa-arrow-right text-[9px]"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═════════════════ FEATURED PRODUCTS ═════════════════ --}}
@if ($featuredProducts->isNotEmpty())
<section class="py-16 sm:py-20" style="background:var(--paper-warm);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-10 reveal">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest" style="color:var(--brand-cyan);">Featured</span>
                <h2 class="display text-3xl sm:text-4xl font-bold mt-2">Our best picks for you</h2>
            </div>
            <a href="{{ route('shop.catalog') }}" class="text-sm font-semibold inline-flex items-center gap-2 hover:gap-3 transition-all" style="color:var(--brand-navy);">
                Shop all <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 reveal-stagger">
            @foreach ($featuredProducts as $product)
                @include('shop.partials.product-card', compact('product'))
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═════════════════ MID BANNER (carousel) ═════════════════ --}}
@if ($midBanners->isNotEmpty())
<section class="py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal">
        <div x-data="{
                active: 0,
                count: {{ $midBanners->count() }},
                timer: null,
                start() { if (this.count > 1) { this.stop(); this.timer = setInterval(() => this.next(), 6000); } },
                stop()  { if (this.timer) { clearInterval(this.timer); this.timer = null; } },
                next()  { this.active = (this.active + 1) % this.count; },
                prev()  { this.active = (this.active - 1 + this.count) % this.count; },
                go(i)   { this.active = i; this.start(); }
             }"
             x-init="start()">
            <div class="relative rounded-3xl overflow-hidden shadow-md aspect-video sm:aspect-[21/9]"
                 @mouseenter="stop()" @mouseleave="start()">

                @foreach ($midBanners as $i => $b)
                    <a href="{{ $b->cta_url ?: route('shop.catalog') }}"
                       class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                       :class="active === {{ $i }} ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'"
                       @if ($i !== 0) style="opacity:0" @endif>
                        <img src="{{ shop_image($b->image) }}" alt="{{ $b->title }}"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0" style="background:linear-gradient(90deg,rgba(12,31,61,.8) 0%,rgba(12,31,61,.2) 60%);"></div>
                        <div class="absolute inset-0 p-8 sm:p-12 flex flex-col justify-center text-white">
                            @if ($b->subtitle) <div class="text-xs font-bold uppercase tracking-widest" style="color:var(--gold);">{{ $b->subtitle }}</div> @endif
                            <h3 class="display text-2xl sm:text-4xl font-bold mt-2 max-w-md">{{ $b->title }}</h3>
                            @if ($b->cta_text)
                                <span class="inline-flex items-center gap-2 mt-4 text-sm font-semibold w-max">
                                    {{ $b->cta_text }} <i class="fas fa-arrow-right text-xs"></i>
                                </span>
                            @endif
                        </div>
                    </a>
                @endforeach

                @if ($midBanners->count() > 1)
                    <button type="button" @click.prevent="prev()" aria-label="Previous slide"
                        class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/80 hover:bg-white text-gray-800 flex items-center justify-center shadow">
                        <i class="fas fa-chevron-left text-sm"></i>
                    </button>
                    <button type="button" @click.prevent="next()" aria-label="Next slide"
                        class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/80 hover:bg-white text-gray-800 flex items-center justify-center shadow">
                        <i class="fas fa-chevron-right text-sm"></i>
                    </button>
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2">
                        @foreach ($midBanners as $i => $b)
                            <button type="button" @click.prevent="go({{ $i }})" aria-label="Go to slide {{ $i + 1 }}"
                                class="h-2.5 rounded-full transition-all"
                                :class="active === {{ $i }} ? 'bg-white w-6' : 'bg-white/50 hover:bg-white/80 w-2.5'"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif

{{-- ═════════════════ NEW ARRIVALS ═════════════════ --}}
@if ($newArrivals->isNotEmpty())
<section class="py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-10 reveal">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest" style="color:var(--brand-cyan);">Fresh</span>
                <h2 class="display text-3xl sm:text-4xl font-bold mt-2">New arrivals</h2>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 reveal-stagger">
            @foreach ($newArrivals as $product)
                @include('shop.partials.product-card', compact('product'))
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═════════════════ BRANDS ═════════════════ --}}
@if ($brands->isNotEmpty())
<section class="py-12 sm:py-16 bg-white border-y border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 reveal">
            <span class="text-xs font-bold uppercase tracking-widest" style="color:var(--brand-cyan);">Brands we love</span>
        </div>
        <div class="flex items-center justify-center flex-wrap gap-8 sm:gap-14 grayscale hover:grayscale-0 transition opacity-70 reveal-stagger">
            @foreach ($brands as $brand)
                <a href="{{ route('shop.brand', $brand->slug) }}" class="block hover:scale-105 transition">
                    @if ($brand->logo)
                        <img src="{{ shop_image($brand->logo) }}" alt="{{ $brand->name }}" class="h-12 object-contain" loading="lazy">
                    @else
                        <span class="text-xl font-bold text-gray-700">{{ $brand->name }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═════════════════ CTA BAND ═════════════════ --}}
<section class="py-16 sm:py-24 relative overflow-hidden" style="background:var(--brand-navy);">
    <div class="absolute inset-0" style="background:radial-gradient(circle at 80% 30%, rgba(251,191,36,.2), transparent 50%);"></div>
    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white reveal">
        <h2 class="display text-3xl sm:text-5xl font-bold mb-4">Discover something new every visit</h2>
        <p class="text-base sm:text-lg text-sky-100/80 mb-8 max-w-xl mx-auto">
            From everyday essentials to special occasion pieces — Almufeed brings the best of our shops directly to your door.
        </p>
        <a href="{{ route('shop.catalog') }}" class="btn btn-primary"><i class="fas fa-bag-shopping"></i> Browse the full catalog</a>
    </div>
</section>

@endsection

@push('styles')
<style>
    .hero-pattern {
        background-image:
            linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
        background-size: 48px 48px;
        mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
        -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
    }
    @keyframes pulse { 0%,100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.4); opacity: .5; } }
</style>
@endpush
