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
            <div class="reveal grid grid-cols-2 gap-4">
                @foreach ($heroBanners->take(4) as $i => $b)
                    <a href="{{ $b->cta_url ?? route('shop.catalog') }}"
                       class="rounded-3xl overflow-hidden block shadow-2xl group {{ $i === 0 ? 'col-span-2' : '' }}"
                       style="aspect-ratio:{{ $i === 0 ? '16/9' : '1/1' }};">
                        <img src="{{ shop_image($b->image) }}" alt="{{ $b->title }}"
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                    </a>
                @endforeach
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

{{-- ═════════════════ MID BANNER ═════════════════ --}}
@if ($midBanners->isNotEmpty())
<section class="py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid sm:grid-cols-2 gap-5 reveal-stagger">
        @foreach ($midBanners as $b)
            <a href="{{ $b->cta_url ?? route('shop.catalog') }}"
               class="group relative rounded-3xl overflow-hidden block shadow-md hover:shadow-2xl transition" style="aspect-ratio:16/9;">
                <img src="{{ shop_image($b->image) }}" alt="{{ $b->title }}"
                     class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                <div class="absolute inset-0" style="background:linear-gradient(90deg,rgba(12,31,61,.8) 0%,rgba(12,31,61,.2) 60%);"></div>
                <div class="absolute inset-0 p-8 sm:p-10 flex flex-col justify-center text-white">
                    @if ($b->subtitle) <div class="text-xs font-bold uppercase tracking-widest" style="color:var(--gold);">{{ $b->subtitle }}</div> @endif
                    <h3 class="display text-3xl sm:text-4xl font-bold mt-2 max-w-sm">{{ $b->title }}</h3>
                    @if ($b->cta_text)
                        <span class="inline-flex items-center gap-2 mt-4 text-sm font-semibold w-max group-hover:gap-3 transition-all">
                            {{ $b->cta_text }} <i class="fas fa-arrow-right text-xs"></i>
                        </span>
                    @endif
                </div>
            </a>
        @endforeach
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
