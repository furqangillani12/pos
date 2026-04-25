<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'ALMUFEED SAQAFTI MARKAZ') }}</title>
    <meta name="description" content="ALMUFEED SAQAFTI MARKAZ — modern point-of-sale, inventory, khata and multi-branch management.">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/mufeed.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite('resources/css/app.css')

    <style>
        /* Brand palette — sourced from the Almufeed logo:
           - Logo blue:  #1f8fc1  (the M / arch)
           - Logo navy:  #0e1f3d  (Arabic text, stars)
           - Accent gold:#fbbf24  (warm contrast on blue) */
        :root {
            --brand-navy: #0c1f3d;
            --brand-blue: #1e3a8a;
            --brand-cyan: #0891b2;
            --brand-light:#1f8fc1;
            --gold:       #fbbf24;
        }
        html, body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, sans-serif; }
        body { background:#f8fafc; color:#1f2937; -webkit-font-smoothing:antialiased; }

        /* Animated gradient background — navy → deep blue → cyan, matches logo */
        .hero-bg {
            background: linear-gradient(120deg, var(--brand-navy), var(--brand-blue), var(--brand-cyan), var(--brand-blue));
            background-size: 300% 300%;
            animation: gradientShift 18s ease infinite;
            position: relative;
            overflow: hidden;
        }
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50%      { background-position: 100% 50%; }
        }
        .hero-bg::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(251,191,36,.18), transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(31,143,193,.25), transparent 45%);
            pointer-events: none;
        }
        .hero-pattern {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
            pointer-events: none;
        }

        /* Floating logo — bright white card so the blue logo pops */
        .logo-card {
            background: rgba(255,255,255,.96);
            border: 1px solid rgba(255,255,255,.5);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 25px 50px -12px rgba(12,31,61,.5), 0 0 0 6px rgba(255,255,255,.08);
            animation: floaty 6s ease-in-out infinite;
        }
        @keyframes floaty {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-10px); }
        }

        /* Primary button — gold (warm contrast on blue) */
        .btn-primary {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            box-shadow: 0 10px 25px -10px rgba(251,191,36,.6);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 30px -10px rgba(251,191,36,.75); }
        .btn-ghost {
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.25);
            backdrop-filter: blur(8px);
            transition: all .2s ease;
        }
        .btn-ghost:hover { background: rgba(255,255,255,.15); }

        /* Feature cards */
        .feature-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
            padding: 28px 24px;
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -20px rgba(8,145,178,.25);
            border-color: var(--brand-cyan);
        }
        .feature-icon {
            width: 56px; height: 56px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 14px;
            font-size: 22px;
            margin-bottom: 16px;
        }

        /* CTA band — navy → cyan */
        .cta-band {
            background: linear-gradient(135deg, var(--brand-navy), var(--brand-cyan));
            position: relative; overflow: hidden;
        }
        .cta-band::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(circle at 90% 30%, rgba(251,191,36,.25), transparent 50%);
        }

        @media (max-width: 640px) {
            .logo-card { padding: 16px !important; }
            .logo-card img { max-height: 90px !important; }
        }
    </style>
</head>
<body>

    {{-- ═══════════════════════════════ NAV ═══════════════════════════════ --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/85 backdrop-blur-md border-b border-gray-200/60"
         style="-webkit-backdrop-filter:blur(12px);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('assets/images/mufeed.png') }}" alt="Almufeed" class="h-9 w-auto">
                <span class="hidden sm:block font-extrabold text-gray-900 tracking-tight">ALMUFEED</span>
            </a>
            <div class="flex items-center gap-2 sm:gap-3">
                @auth
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white text-sm font-semibold"
                       style="background:linear-gradient(135deg,#0c1f3d,#0891b2);">
                        <i class="fas fa-tachometer-alt text-xs"></i>
                        <span class="hidden sm:inline">Go to Dashboard</span><span class="sm:hidden">Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white text-sm font-semibold"
                       style="background:linear-gradient(135deg,#0c1f3d,#0891b2);">
                        <i class="fas fa-sign-in-alt text-xs"></i> Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ═══════════════════════════════ HERO ═══════════════════════════════ --}}
    <section class="hero-bg pt-28 sm:pt-32 pb-20 sm:pb-28 px-4 sm:px-6 lg:px-8 text-white">
        <div class="hero-pattern"></div>
        <div class="relative max-w-6xl mx-auto text-center">

            {{-- Floating logo card --}}
            <div class="logo-card inline-flex items-center justify-center rounded-3xl px-8 py-6 mb-8">
                <img src="{{ asset('assets/images/mufeed.png') }}" alt="Almufeed Saqafti Markaz" class="max-h-28 sm:max-h-32 w-auto drop-shadow-2xl">
            </div>

            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold mb-5"
                 style="background:rgba(251,191,36,.15);border:1px solid rgba(251,191,36,.3);color:#fde68a;">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                Trusted Retail Management Platform
            </div>

            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight mb-4 leading-tight">
                ALMUFEED <span style="color:#fbbf24;">SAQAFTI</span> MARKAZ
            </h1>
            <p class="text-base sm:text-lg md:text-xl text-sky-100/90 max-w-2xl mx-auto mb-3">
                Your trusted place for quality and affordability.
            </p>
            <p class="text-sm sm:text-base text-sky-100/70 max-w-xl mx-auto mb-10">
                A complete point-of-sale, inventory and khata management system — built for multi-branch retail.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
                @auth
                    <a href="{{ route('admin.dashboard') }}"
                       class="btn-primary inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl text-gray-900 font-bold text-base w-full sm:w-auto">
                        <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                    </a>
                    <a href="{{ route('admin.pos.index') }}"
                       class="btn-ghost inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl text-white font-bold text-base w-full sm:w-auto">
                        <i class="fas fa-cash-register"></i> Open POS
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="btn-primary inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl text-gray-900 font-bold text-base w-full sm:w-auto">
                        <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                    </a>
                    <a href="#features"
                       class="btn-ghost inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl text-white font-bold text-base w-full sm:w-auto">
                        <i class="fas fa-circle-info"></i> Learn More
                    </a>
                @endauth
            </div>

            {{-- Stats row --}}
            <div class="grid grid-cols-3 gap-4 sm:gap-8 max-w-3xl mx-auto mt-16 pt-10 border-t border-white/15">
                <div>
                    <div class="text-2xl sm:text-4xl font-extrabold" style="color:#fbbf24;">Multi</div>
                    <div class="text-[11px] sm:text-sm text-sky-100/80 mt-1">Branch Support</div>
                </div>
                <div>
                    <div class="text-2xl sm:text-4xl font-extrabold" style="color:#fbbf24;">24/7</div>
                    <div class="text-[11px] sm:text-sm text-sky-100/80 mt-1">Always Available</div>
                </div>
                <div>
                    <div class="text-2xl sm:text-4xl font-extrabold" style="color:#fbbf24;">100%</div>
                    <div class="text-[11px] sm:text-sm text-sky-100/80 mt-1">Khata Tracking</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════ FEATURES ═══════════════════════════════ --}}
    <section id="features" class="py-16 sm:py-24 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
                <span class="inline-block text-xs font-bold uppercase tracking-widest text-cyan-700 mb-3">Features</span>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">
                    Everything you need to run your business
                </h2>
                <p class="text-base text-gray-600">From the cash counter to the back office — one platform, every branch.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @php
                    $features = [
                        ['icon'=>'fa-cash-register','title'=>'POS Terminal','desc'=>'Lightning-fast checkout with barcode scan, tier pricing, weight-based delivery and thermal receipts.','bg'=>'#ecfdf5','color'=>'#059669'],
                        ['icon'=>'fa-store-alt','title'=>'Multi-Branch','desc'=>'Independent stock, sales and reports per branch — switch on the fly with a single account.','bg'=>'#eff6ff','color'=>'#2563eb'],
                        ['icon'=>'fa-book-open','title'=>'Khata / Credit','desc'=>'Pakistani-style customer credit ledger with limits, due dates, statements and overdue tracking.','bg'=>'#fef3c7','color'=>'#d97706'],
                        ['icon'=>'fa-boxes','title'=>'Inventory & Stock','desc'=>'Per-branch stock, low-stock alerts, audit logs, Excel import / export and barcode generation.','bg'=>'#f3e8ff','color'=>'#7c3aed'],
                        ['icon'=>'fa-money-bill-wave','title'=>'Cash In / Out','desc'=>'One screen for customer, supplier and ledger cash transactions with full double-entry.','bg'=>'#fee2e2','color'=>'#dc2626'],
                        ['icon'=>'fa-chart-line','title'=>'Reports & Analytics','desc'=>'Sales, profit-and-loss, top products, customer analysis — filter by date, branch and category.','bg'=>'#cffafe','color'=>'#0891b2'],
                    ];
                @endphp
                @foreach ($features as $f)
                    <div class="feature-card">
                        <div class="feature-icon" style="background:{{ $f['bg'] }};color:{{ $f['color'] }};">
                            <i class="fas {{ $f['icon'] }}"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $f['title'] }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════ CTA BAND ═══════════════════════════════ --}}
    <section class="cta-band py-16 sm:py-20 px-4 sm:px-6 lg:px-8 text-white">
        <div class="relative max-w-4xl mx-auto text-center">
            <div class="inline-flex items-center gap-3 bg-white/10 backdrop-blur px-4 py-2 rounded-full mb-5 border border-white/20">
                <img src="{{ asset('assets/images/mufeed.png') }}" alt="" class="h-6 w-auto">
                <span class="text-sm font-semibold">ALMUFEED SAQAFTI MARKAZ</span>
            </div>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 leading-tight">
                Ready to manage your business better?
            </h2>
            <p class="text-base sm:text-lg text-sky-100 max-w-xl mx-auto mb-8">
                Sign in to access the dashboard, POS, inventory, khata and reports — all in one place.
            </p>
            @auth
                <a href="{{ route('admin.dashboard') }}"
                   class="btn-primary inline-flex items-center gap-2 px-8 py-4 rounded-xl text-gray-900 font-bold text-base">
                    <i class="fas fa-arrow-right-to-bracket"></i> Go to Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="btn-primary inline-flex items-center gap-2 px-8 py-4 rounded-xl text-gray-900 font-bold text-base">
                    <i class="fas fa-sign-in-alt"></i> Sign in to your account
                </a>
            @endauth
        </div>
    </section>

    {{-- ═══════════════════════════════ FOOTER ═══════════════════════════════ --}}
    <footer class="bg-gray-900 text-gray-400 py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/images/mufeed.png') }}" alt="Almufeed" class="h-10 w-auto opacity-90">
                <div>
                    <div class="font-bold text-white">ALMUFEED SAQAFTI MARKAZ</div>
                    <div class="text-xs">Quality &amp; affordability since day one.</div>
                </div>
            </div>
            <div class="text-xs text-center sm:text-right">
                &copy; {{ now()->year }} ALMUFEED SAQAFTI MARKAZ. All rights reserved.<br>
                <span class="text-gray-500">www.almufeed.com.pk</span>
            </div>
        </div>
    </footer>

</body>
</html>
