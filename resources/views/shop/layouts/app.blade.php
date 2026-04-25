<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0c1f3d">

    <title>@yield('title', 'Almufeed') · Almufeed Point Of Sale</title>
    <meta name="description" content="@yield('description', 'Almufeed — quality and affordability you can trust.')">

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%230c1f3d'/><text x='16' y='22' text-anchor='middle' font-family='Inter,sans-serif' font-size='16' font-weight='800' fill='%23fbbf24'>A</text></svg>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --brand-navy:  #0c1f3d;
            --brand-blue:  #1e3a8a;
            --brand-cyan:  #0891b2;
            --brand-light: #1f8fc1;
            --gold:        #fbbf24;
            --gold-deep:   #d97706;
            --paper:       #fafaf7;
            --paper-warm:  #f5f1e8;
        }
        html, body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            background: var(--paper);
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }
        h1, h2, h3, .display { font-family: 'Playfair Display', Georgia, serif; letter-spacing: -0.01em; }
        body { overflow-x: hidden; }
        a { color: inherit; text-decoration: none; }
        [x-cloak] { display: none !important; }

        /* ── Smooth nav transitions ────────────────────────────────────── */
        @view-transition { navigation: auto; }
        ::view-transition-old(root) { animation: fadeOut .2s ease both; }
        ::view-transition-new(root) { animation: fadeIn  .25s ease both; }
        @keyframes fadeIn  { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }

        /* ── Reveal-on-scroll ─────────────────────────────────────────── */
        .reveal { opacity: 0; transform: translateY(20px); transition: opacity .7s ease, transform .7s ease; }
        .reveal.in { opacity: 1; transform: none; }
        .reveal-stagger > * { opacity: 0; transform: translateY(20px); transition: opacity .7s ease, transform .7s ease; }
        .reveal-stagger.in > *:nth-child(1) { transition-delay: 0ms; }
        .reveal-stagger.in > *:nth-child(2) { transition-delay: 80ms; }
        .reveal-stagger.in > *:nth-child(3) { transition-delay: 160ms; }
        .reveal-stagger.in > *:nth-child(4) { transition-delay: 240ms; }
        .reveal-stagger.in > *:nth-child(5) { transition-delay: 320ms; }
        .reveal-stagger.in > *:nth-child(6) { transition-delay: 400ms; }
        .reveal-stagger.in > *:nth-child(n+7) { transition-delay: 480ms; }
        .reveal-stagger.in > * { opacity: 1; transform: none; }

        /* ── Buttons ──────────────────────────────────────────────────── */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px;
               padding: 11px 22px; border-radius: 12px; font-weight: 600; font-size: 14px;
               transition: transform .15s ease, box-shadow .2s ease, background .2s ease, color .2s ease;
               border: 1px solid transparent; cursor: pointer; user-select: none; }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary { background: linear-gradient(135deg, var(--gold), var(--gold-deep)); color: #111827;
                       box-shadow: 0 10px 25px -10px rgba(251,191,36,.55); }
        .btn-primary:hover { box-shadow: 0 14px 30px -10px rgba(251,191,36,.75); }
        .btn-dark { background: linear-gradient(135deg, var(--brand-navy), var(--brand-cyan)); color: #fff;
                    box-shadow: 0 10px 25px -10px rgba(12,31,61,.45); }
        .btn-ghost { background: transparent; border-color: rgba(0,0,0,.12); color: #1f2937; }
        .btn-ghost:hover { background: #fff; border-color: rgba(0,0,0,.25); }
        .btn-block { width: 100%; }

        /* ── Pill / chip ──────────────────────────────────────────────── */
        .chip { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px;
                border-radius: 9999px; font-size: 11px; font-weight: 600; letter-spacing: .02em; }

        /* ── Cards ────────────────────────────────────────────────────── */
        .product-card {
            background: #fff; border: 1px solid rgba(0,0,0,.06); border-radius: 18px; overflow: hidden;
            transition: transform .25s cubic-bezier(.2,.8,.2,1), box-shadow .25s ease, border-color .25s ease;
            display: flex; flex-direction: column;
        }
        .product-card:hover { transform: translateY(-6px); box-shadow: 0 25px 40px -25px rgba(8,32,75,.25); border-color: rgba(8,145,178,.35); }
        .product-card .img-wrap { position: relative; aspect-ratio: 4/5; background: var(--paper-warm); overflow: hidden; }
        .product-card .img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform .6s cubic-bezier(.2,.8,.2,1); }
        .product-card:hover .img-wrap img { transform: scale(1.06); }
        .product-card .quick { position: absolute; top: 12px; right: 12px; display: flex; flex-direction: column; gap: 8px; opacity: 0; transform: translateX(8px); transition: opacity .25s ease, transform .25s ease; }
        .product-card:hover .quick { opacity: 1; transform: none; }
        .product-card .quick button {
            width: 36px; height: 36px; border-radius: 9999px; background: #fff; color: var(--brand-navy);
            border: 1px solid rgba(0,0,0,.05); display: flex; align-items: center; justify-content: center;
            cursor: pointer; box-shadow: 0 6px 12px -4px rgba(0,0,0,.15); transition: background .2s ease, color .2s ease;
        }
        .product-card .quick button:hover { background: var(--brand-navy); color: var(--gold); }

        /* ── Hero ─────────────────────────────────────────────────────── */
        .hero {
            background: linear-gradient(120deg, var(--brand-navy), var(--brand-blue), var(--brand-cyan), var(--brand-blue));
            background-size: 300% 300%;
            animation: heroShift 18s ease infinite;
            position: relative; overflow: hidden; color: #fff;
        }
        @keyframes heroShift { 0%,100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
        .hero::before {
            content:''; position: absolute; inset: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(251,191,36,.18), transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(31,143,193,.25), transparent 45%);
            pointer-events: none;
        }

        /* ── Mini cart drawer ─────────────────────────────────────────── */
        .drawer-overlay { position: fixed; inset: 0; background: rgba(15,23,42,.5); backdrop-filter: blur(2px); z-index: 90; opacity: 0; pointer-events: none; transition: opacity .3s ease; }
        .drawer-overlay.open { opacity: 1; pointer-events: auto; }
        .drawer { position: fixed; top: 0; right: 0; bottom: 0; width: 100%; max-width: 420px; background: #fff;
                  z-index: 100; transform: translateX(100%); transition: transform .35s cubic-bezier(.2,.8,.2,1);
                  display: flex; flex-direction: column; box-shadow: -20px 0 60px -20px rgba(0,0,0,.25); }
        .drawer.open { transform: translateX(0); }

        /* ── Toast ─────────────────────────────────────────────────────── */
        .toast-stack { position: fixed; bottom: 24px; right: 24px; z-index: 200; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
        .toast { background: #fff; border: 1px solid rgba(0,0,0,.06); border-radius: 12px;
                 padding: 12px 16px; min-width: 240px; max-width: 360px; font-size: 14px;
                 box-shadow: 0 20px 40px -15px rgba(0,0,0,.25);
                 display: flex; align-items: start; gap: 10px;
                 transform: translateX(20px); opacity: 0; pointer-events: auto;
                 transition: transform .35s cubic-bezier(.2,.8,.2,1), opacity .3s ease;
                 border-left: 4px solid var(--brand-cyan); }
        .toast.show { transform: none; opacity: 1; }
        .toast.success { border-left-color: #059669; }
        .toast.error   { border-left-color: #dc2626; }

        /* ── Skeleton ─────────────────────────────────────────────────── */
        .skel { background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%); background-size: 200% 100%; animation: skel 1.4s linear infinite; border-radius: 8px; }
        @keyframes skel { from { background-position: 200% 0; } to { background-position: -200% 0; } }

        /* ── Header ──────────────────────────────────────────────────── */
        .nav-link { position: relative; padding: 6px 0; font-weight: 500; }
        .nav-link::after { content:''; position: absolute; left: 0; right: 100%; bottom: 0; height: 2px; background: var(--gold); transition: right .3s ease; }
        .nav-link:hover::after, .nav-link.active::after { right: 0; }

        /* ── Smooth image fade-in ────────────────────────────────────── */
        img.lazy { opacity: 0; transition: opacity .4s ease; }
        img.lazy.loaded { opacity: 1; }

        /* ── Page wrapper transition ─────────────────────────────────── */
        main { animation: pageIn .35s cubic-bezier(.2,.8,.2,1) both; }
        @keyframes pageIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
    </style>
    @stack('styles')
</head>
<body x-data="{
        miniCartOpen: false,
        mobileNavOpen: false,
        searchOpen: false,
        cartCount: {{ shop_cart_count() }},
    }"
    x-init="
        // reveal-on-scroll
        const io = new IntersectionObserver((es) => es.forEach(e => e.isIntersecting && e.target.classList.add('in')), {threshold: .12});
        document.querySelectorAll('.reveal, .reveal-stagger').forEach(el => io.observe(el));
        // lazy images
        const li = new IntersectionObserver((es, obs) => es.forEach(e => { if (e.isIntersecting) { const i = e.target; if (i.dataset.src) { i.src = i.dataset.src; } i.addEventListener('load', () => i.classList.add('loaded')); obs.unobserve(i); }}), {rootMargin: '200px'});
        document.querySelectorAll('img.lazy[data-src]').forEach(i => li.observe(i));
        // prefetch on hover for snappier nav
        document.body.addEventListener('mouseover', (e) => {
            const a = e.target.closest('a[href]');
            if (!a || a.dataset.prefetched) return;
            const u = new URL(a.href, location.href);
            if (u.origin !== location.origin) return;
            a.dataset.prefetched = '1';
            const l = document.createElement('link'); l.rel = 'prefetch'; l.href = a.href; document.head.appendChild(l);
        });
    ">

    {{-- ═════════════════ Announcement bar ═════════════════ --}}
    <div class="text-white text-xs font-medium" style="background:var(--brand-navy);">
        <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-center gap-2">
            <i class="fas fa-truck text-[10px]" style="color:var(--gold);"></i>
            <span>Free delivery across Pakistan on orders above Rs. 5,000</span>
            <span class="hidden sm:inline opacity-60 mx-2">·</span>
            <span class="hidden sm:inline">Trusted retail · Almufeed Point Of Sale</span>
        </div>
    </div>

    {{-- ═════════════════ Header ═════════════════ --}}
    <header class="sticky top-0 z-40 bg-white/85 backdrop-blur-md border-b border-gray-200/60"
            style="-webkit-backdrop-filter:blur(12px);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center gap-4">
            <button @click="mobileNavOpen = true" class="lg:hidden text-gray-700 text-xl"><i class="fas fa-bars"></i></button>

            <a href="{{ route('shop.home') }}" class="flex items-center gap-2 mr-4 group">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-white font-extrabold text-sm transition group-hover:scale-110"
                      style="background:linear-gradient(135deg,var(--brand-navy),var(--brand-cyan));">A</span>
                <div class="leading-tight">
                    <div class="font-extrabold text-gray-900 tracking-tight text-base">Almufeed</div>
                    <div class="text-[10px] uppercase tracking-widest" style="color:var(--brand-cyan);">Point of Sale</div>
                </div>
            </a>

            <nav class="hidden lg:flex items-center gap-6 text-sm text-gray-700">
                <a href="{{ route('shop.home') }}"      class="nav-link {{ request()->routeIs('shop.home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('shop.catalog') }}"   class="nav-link {{ request()->routeIs('shop.catalog') ? 'active' : '' }}">Shop</a>
                @php $featuredCats = \App\Models\Category::active()->where('is_featured', true)->orderBy('sort_order')->limit(4)->get(); @endphp
                @foreach ($featuredCats as $cat)
                    <a href="{{ route('shop.category', $cat->slug) }}" class="nav-link">{{ $cat->name }}</a>
                @endforeach
                <a href="{{ route('shop.about') }}"     class="nav-link">About</a>
                <a href="{{ route('shop.contact') }}"   class="nav-link">Contact</a>
            </nav>

            <div class="ml-auto flex items-center gap-2">
                <button @click="searchOpen = !searchOpen" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-700"><i class="fas fa-search"></i></button>

                @auth('customer')
                    <a href="{{ route('shop.account') }}" class="hidden sm:inline-flex w-10 h-10 rounded-full hover:bg-gray-100 items-center justify-center text-gray-700" title="My account"><i class="fas fa-user"></i></a>
                    <a href="{{ route('shop.wishlist') }}" class="hidden sm:inline-flex w-10 h-10 rounded-full hover:bg-gray-100 items-center justify-center text-gray-700" title="Wishlist"><i class="fas fa-heart"></i></a>
                @else
                    <a href="{{ route('shop.login') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user text-xs"></i> Sign in
                    </a>
                @endauth

                <button @click="miniCartOpen = true" class="relative w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-800" title="Cart">
                    <i class="fas fa-shopping-bag"></i>
                    <span x-show="cartCount > 0" x-cloak x-text="cartCount" class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-amber-400 text-[10px] font-bold text-gray-900 flex items-center justify-center"></span>
                </button>
            </div>
        </div>

        {{-- Search overlay --}}
        <div x-show="searchOpen" x-cloak x-transition.opacity class="border-t border-gray-200/60 bg-white/95 backdrop-blur">
            <form action="{{ route('shop.search') }}" method="GET" class="max-w-3xl mx-auto px-4 py-4 flex items-center gap-3">
                <i class="fas fa-search text-gray-400"></i>
                <input type="text" name="q" value="{{ request('q') }}" autofocus placeholder="Search products, brands, categories..."
                       class="flex-1 bg-transparent outline-none text-base">
                <button type="button" @click="searchOpen = false" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times"></i></button>
            </form>
        </div>
    </header>

    {{-- ═════════════════ Flash messages ═════════════════ --}}
    @include('shop.partials.flash')

    {{-- ═════════════════ Page content ═════════════════ --}}
    <main class="min-h-[60vh]">
        @yield('content')
    </main>

    {{-- ═════════════════ Footer ═════════════════ --}}
    <footer class="mt-20 bg-gray-900 text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-white font-extrabold text-sm" style="background:linear-gradient(135deg,var(--brand-navy),var(--brand-cyan));">A</span>
                    <div>
                        <div class="font-bold text-white">Almufeed</div>
                        <div class="text-[10px] uppercase tracking-widest" style="color:var(--gold);">Point of Sale</div>
                    </div>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed">Quality and affordability you can trust. Multi-branch retail platform powering the storefront and our shops.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3 text-sm uppercase tracking-wider">Shop</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('shop.catalog') }}" class="hover:text-white">All products</a></li>
                    @foreach ($featuredCats ?? collect() as $cat)
                        <li><a href="{{ route('shop.category', $cat->slug) }}" class="hover:text-white">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3 text-sm uppercase tracking-wider">Help</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('shop.about') }}" class="hover:text-white">About us</a></li>
                    <li><a href="{{ route('shop.contact') }}" class="hover:text-white">Contact</a></li>
                    <li><a href="{{ route('shop.returns') }}" class="hover:text-white">Returns &amp; refunds</a></li>
                    <li><a href="{{ route('shop.privacy') }}" class="hover:text-white">Privacy policy</a></li>
                    <li><a href="{{ route('shop.terms') }}" class="hover:text-white">Terms</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3 text-sm uppercase tracking-wider">Stay in touch</h4>
                <p class="text-sm text-gray-400 mb-3">New arrivals and offers, straight to your inbox.</p>
                <form class="flex gap-2">
                    <input type="email" placeholder="you@example.com" class="flex-1 px-3 py-2 rounded-lg bg-gray-800 border border-gray-700 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
                    <button type="submit" class="btn btn-primary !py-2 !px-4 !text-xs">Join</button>
                </form>
                <div class="flex items-center gap-3 mt-4 text-gray-400">
                    <a href="#" class="w-9 h-9 rounded-full bg-gray-800 hover:bg-gray-700 flex items-center justify-center"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-9 h-9 rounded-full bg-gray-800 hover:bg-gray-700 flex items-center justify-center"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="w-9 h-9 rounded-full bg-gray-800 hover:bg-gray-700 flex items-center justify-center"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 py-5 text-center text-xs text-gray-500">
            &copy; {{ now()->year }} Almufeed Point Of Sale. All rights reserved.
        </div>
    </footer>

    {{-- ═════════════════ Mobile nav drawer ═════════════════ --}}
    <div class="drawer-overlay" :class="mobileNavOpen ? 'open' : ''" @click="mobileNavOpen = false"></div>
    <div class="drawer" :class="mobileNavOpen ? 'open' : ''" style="max-width:320px;">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <span class="font-bold text-gray-900">Menu</span>
            <button @click="mobileNavOpen = false" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times"></i></button>
        </div>
        <nav class="flex-1 overflow-y-auto p-5 space-y-2 text-sm">
            <a href="{{ route('shop.home') }}"     class="block py-2 hover:text-cyan-700 font-semibold">Home</a>
            <a href="{{ route('shop.catalog') }}"  class="block py-2 hover:text-cyan-700 font-semibold">Shop</a>
            @foreach ($featuredCats ?? collect() as $cat)
                <a href="{{ route('shop.category', $cat->slug) }}" class="block py-2 hover:text-cyan-700">{{ $cat->name }}</a>
            @endforeach
            <hr class="border-gray-200 my-3">
            @auth('customer')
                <a href="{{ route('shop.account') }}" class="block py-2"><i class="fas fa-user mr-2 text-gray-400"></i> My account</a>
                <a href="{{ route('shop.wishlist') }}" class="block py-2"><i class="fas fa-heart mr-2 text-gray-400"></i> Wishlist</a>
                <form method="POST" action="{{ route('shop.logout') }}">
                    @csrf
                    <button class="block py-2 text-rose-600"><i class="fas fa-sign-out-alt mr-2"></i> Sign out</button>
                </form>
            @else
                <a href="{{ route('shop.login') }}"    class="block py-2"><i class="fas fa-sign-in-alt mr-2 text-gray-400"></i> Sign in</a>
                <a href="{{ route('shop.register') }}" class="block py-2"><i class="fas fa-user-plus mr-2 text-gray-400"></i> Create account</a>
            @endauth
            <hr class="border-gray-200 my-3">
            <a href="{{ route('shop.about') }}"   class="block py-2">About</a>
            <a href="{{ route('shop.contact') }}" class="block py-2">Contact</a>
        </nav>
    </div>

    {{-- ═════════════════ Mini cart drawer ═════════════════ --}}
    <div class="drawer-overlay" :class="miniCartOpen ? 'open' : ''" @click="miniCartOpen = false"></div>
    <div class="drawer" :class="miniCartOpen ? 'open' : ''" x-data="miniCart()" x-init="$watch('$root.miniCartOpen', v => v && load())">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <span class="font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-shopping-bag"></i> Your bag <span class="text-xs text-gray-500" x-text="'(' + items.length + ')'"></span></span>
            <button @click="$root.miniCartOpen = false" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times"></i></button>
        </div>
        <div class="flex-1 overflow-y-auto p-5">
            <template x-if="loading">
                <div class="space-y-3">
                    <div class="skel h-20"></div>
                    <div class="skel h-20"></div>
                </div>
            </template>
            <template x-if="!loading && items.length === 0">
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-3 block"></i>
                    <p class="font-semibold">Your bag is empty</p>
                    <p class="text-xs mt-1">Add some beautiful pieces to it.</p>
                    <a href="{{ route('shop.catalog') }}" @click="$root.miniCartOpen = false" class="btn btn-dark mt-4 !text-xs">Start shopping</a>
                </div>
            </template>
            <template x-if="!loading && items.length > 0">
                <div class="space-y-3">
                    <template x-for="it in items" :key="it.id">
                        <div class="flex gap-3 p-3 rounded-xl border border-gray-100 hover:border-gray-200 bg-white">
                            <img :src="it.image" alt="" class="w-16 h-20 object-cover rounded-lg" style="background:#f5f1e8;">
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-gray-800 truncate" x-text="it.name"></div>
                                <div class="text-[11px] text-gray-500 mt-0.5" x-text="'Qty ' + it.qty"></div>
                                <div class="text-sm font-bold mt-1" style="color:var(--brand-cyan);" x-text="'Rs. ' + (it.qty * it.unit_price).toLocaleString()"></div>
                            </div>
                            <button @click="remove(it.id)" class="text-gray-300 hover:text-rose-500 self-start"><i class="fas fa-times-circle"></i></button>
                        </div>
                    </template>
                </div>
            </template>
        </div>
        <div class="border-t border-gray-200 p-5" x-show="items.length > 0">
            <div class="flex items-center justify-between mb-4">
                <span class="text-gray-500 text-sm">Subtotal</span>
                <span class="font-bold text-lg" style="color:var(--brand-navy);" x-text="'Rs. ' + subtotal.toLocaleString()"></span>
            </div>
            <a href="{{ route('shop.cart') }}" class="btn btn-ghost btn-block mb-2">View bag</a>
            <a href="{{ route('shop.checkout') }}" class="btn btn-primary btn-block">Checkout <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
    </div>

    {{-- ═════════════════ Toast stack ═════════════════ --}}
    <div class="toast-stack" id="toastStack"></div>

    @stack('scripts')

    <script>
        // ── Toast helper ─────────────────────────────────────────────────
        window.toast = function (msg, type = 'info') {
            const stack = document.getElementById('toastStack');
            const t = document.createElement('div');
            t.className = 'toast ' + (type === 'success' ? 'success' : type === 'error' ? 'error' : '');
            const icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-info';
            const color = type === 'success' ? '#059669' : type === 'error' ? '#dc2626' : '#0891b2';
            t.innerHTML = `<i class="fas ${icon} text-base mt-0.5" style="color:${color};"></i><div class="flex-1 text-gray-700">${msg}</div>`;
            stack.appendChild(t);
            requestAnimationFrame(() => t.classList.add('show'));
            setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 350); }, 3500);
        };

        // ── Mini cart Alpine component ───────────────────────────────────
        function miniCart() {
            return {
                items: [], subtotal: 0, loading: false,
                async load() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route('shop.cart.json') }}', {headers: {'Accept': 'application/json'}});
                        const data = await res.json();
                        this.items = data.items || [];
                        this.subtotal = data.subtotal || 0;
                        this.$root.cartCount = (data.items || []).reduce((s, i) => s + Number(i.qty), 0);
                    } finally { this.loading = false; }
                },
                async remove(id) {
                    const res = await fetch('/shop/cart/remove/' + id, {
                        method: 'DELETE',
                        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json'}
                    });
                    if (res.ok) { window.toast('Removed from bag', 'success'); this.load(); }
                    else        { window.toast('Could not remove', 'error'); }
                },
            };
        }

        // ── Add-to-cart helper (used from product cards / detail page) ───
        window.addToCart = async function (productId, qty = 1, opts = {}) {
            const fd = new FormData();
            fd.append('product_id', productId);
            fd.append('qty', qty);
            if (opts.size)  fd.append('size',  opts.size);
            if (opts.color) fd.append('color', opts.color);
            try {
                const res = await fetch('{{ route('shop.cart.add') }}', {
                    method: 'POST', body: fd,
                    headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json'}
                });
                const data = await res.json();
                if (!res.ok || !data.ok) { window.toast(data.message || 'Could not add', 'error'); return false; }
                window.toast(data.message || 'Added to bag', 'success');
                document.dispatchEvent(new CustomEvent('cart:changed', { detail: data }));
                return true;
            } catch (e) { window.toast('Network error', 'error'); return false; }
        };
        document.addEventListener('cart:changed', (e) => {
            const root = document.body.__x?.$data;
            if (root && e.detail && typeof e.detail.cart_count !== 'undefined') {
                root.cartCount = e.detail.cart_count;
            }
        });

        // ── Wishlist toggle helper ───────────────────────────────────────
        window.toggleWishlist = async function (productId, btn) {
            try {
                const res = await fetch('/shop/wishlist/toggle/' + productId, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json'}
                });
                if (res.status === 401) { window.location = '{{ route('shop.login') }}'; return; }
                const data = await res.json();
                if (data.ok) {
                    btn?.classList.toggle('text-rose-500', data.in_wishlist);
                    btn?.querySelector('i')?.classList.toggle('fas', data.in_wishlist);
                    btn?.querySelector('i')?.classList.toggle('far', !data.in_wishlist);
                    window.toast(data.in_wishlist ? 'Added to wishlist' : 'Removed from wishlist', 'success');
                }
            } catch(e) { window.toast('Network error', 'error'); }
        };
    </script>
</body>
</html>
