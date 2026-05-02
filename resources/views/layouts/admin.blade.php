<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Dashboard') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>
        /* Mobile-specific styles */
        @media (max-width: 768px) {
            .mobile-sidebar {
                position: fixed;
                left: 0;
                top: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                z-index: 50;
                height: 100vh;
                overflow-y: auto;
            }

            .mobile-sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 49;
            }

            .sidebar-overlay.open {
                display: block;
            }

            /* Shift main content when sidebar opens */
            .main-content-mobile {
                transition: transform 0.3s ease-in-out;
            }

            .main-content-mobile.shifted {
                transform: translateX(64px);
            }

            /* Fix for mobile header */
            .mobile-header {
                width: 100%;
                max-width: 100%;
                left: 0;
                right: 0;
            }
        }

        /* Fix for desktop sidebar */
        @media (min-width: 769px) {
            .mobile-sidebar {
                position: relative;
                transform: none !important;
            }

            .mobile-header {
                display: none !important;
            }

            .mobile-sidebar-header {
                display: none !important;
            }
        }

        @media (max-width: 769px) {
            .main-div {
                margin-top: 50px;
            }
        }

        /* ══════════════════════════════════════════════════════════
           GLOBAL MOBILE RESPONSIVE FIXES
           Applied to all admin screens
        ══════════════════════════════════════════════════════════ */
        @media (max-width: 768px) {

            /* ── Main content area ── */
            main.flex-1 {
                padding: 12px !important;
                padding-top: 70px !important;
            }

            .main-div {
                margin-top: 0 !important;
            }

            /* ── Tables: horizontal scroll + compact cells ── */
            table {
                font-size: 13px;
            }

            table th,
            table td {
                padding: 8px 6px !important;
                font-size: 12px;
            }

            table th {
                font-size: 11px;
                white-space: nowrap;
            }

            /* Ensure table wrappers scroll */
            .overflow-x-auto {
                -webkit-overflow-scrolling: touch;
            }

            /* ── Buttons: better touch targets & wrapping ── */
            .space-x-2 > * + *,
            .space-x-3 > * + * {
                margin-left: 4px !important;
            }

            /* Make inline action buttons wrap */
            td .flex,
            td .inline-flex,
            td .space-x-2,
            td .space-x-3 {
                flex-wrap: wrap;
                gap: 4px;
            }

            /* Minimum touch target for buttons/links in tables */
            td a, td button {
                min-height: 32px;
                min-width: 32px;
            }

            /* ── Forms: stack filter rows ── */
            .flex.items-center.space-x-2,
            .flex.items-center.space-x-3,
            .flex.items-center.space-x-4 {
                flex-wrap: wrap;
                gap: 8px;
            }

            /* ── Page headers: wrap title + buttons ── */
            .flex.justify-between.items-center {
                flex-wrap: wrap;
                gap: 8px;
            }

            /* ── Modals: constrain to screen ── */
            .fixed.inset-0 .bg-white,
            .fixed.inset-0 [class*="w-96"],
            .fixed.inset-0 [class*="max-w-"] {
                width: 92vw !important;
                max-width: 92vw !important;
                margin: 0 auto;
            }

            /* ── Cards & containers: reduce padding ── */
            .p-6 {
                padding: 12px !important;
            }

            .p-8 {
                padding: 14px !important;
            }

            .px-6 {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }

            .px-8 {
                padding-left: 14px !important;
                padding-right: 14px !important;
            }

            /* ── Text: slightly smaller on mobile ── */
            .text-2xl {
                font-size: 1.25rem !important;
            }

            .text-3xl {
                font-size: 1.5rem !important;
            }

            h1.text-2xl, h2.text-2xl {
                font-size: 1.15rem !important;
            }

            /* ── Grid fixes: force cols on tiny screens ── */
            .grid.grid-cols-2:not(.md\:grid-cols-2) {
                grid-template-columns: repeat(2, 1fr);
            }

            .grid.grid-cols-3 {
                grid-template-columns: repeat(2, 1fr) !important;
            }

            .grid.grid-cols-4 {
                grid-template-columns: repeat(2, 1fr) !important;
            }

            /* ── Footer action rows: stack vertically ── */
            .flex.justify-end.space-x-3,
            .flex.justify-end.space-x-2 {
                flex-direction: column;
                gap: 8px;
                width: 100%;
            }

            .flex.justify-end.space-x-3 > *,
            .flex.justify-end.space-x-2 > * {
                margin-left: 0 !important;
                width: 100%;
                text-align: center;
            }

            .flex.justify-end.space-x-3 a,
            .flex.justify-end.space-x-3 button,
            .flex.justify-end.space-x-2 a,
            .flex.justify-end.space-x-2 button {
                width: 100%;
                justify-content: center;
            }

            /* ── Stat cards: ensure readable ── */
            .grid .bg-white.rounded-lg,
            .grid .bg-white.shadow,
            .grid .dark\:bg-gray-800 {
                padding: 10px !important;
            }

            /* ── Whitespace fixes ── */
            .whitespace-nowrap {
                white-space: normal !important;
            }

            /* ── Max-width containers: full width on mobile ── */
            .max-w-2xl,
            .max-w-3xl,
            .max-w-4xl,
            .max-w-5xl,
            .max-w-6xl,
            .max-w-7xl {
                max-width: 100% !important;
            }
        }

        /* Very small screens (< 400px) */
        @media (max-width: 400px) {
            main.flex-1 {
                padding: 8px !important;
                padding-top: 68px !important;
            }

            table th, table td {
                padding: 6px 4px !important;
                font-size: 11px;
            }

            .grid.grid-cols-2 {
                grid-template-columns: 1fr !important;
            }
        }
        /* ══════════════════════════════════════════════════════════
           GLOBAL DARK MODE — applies to ALL admin pages
           Uses prefers-color-scheme (Tailwind default media strategy)
        ══════════════════════════════════════════════════════════ */
        @media (prefers-color-scheme: dark) {

            /* ── Base cards & containers ── */
            .bg-white,
            .bg-gray-50 {
                background-color: #1f2937 !important;
                color: #e5e7eb !important;
            }

            .bg-gray-100 {
                background-color: #111827 !important;
            }

            /* ── Text colors ── */
            .text-gray-800,
            .text-gray-900,
            .text-gray-700 {
                color: #e5e7eb !important;
            }

            .text-gray-600,
            .text-gray-500 {
                color: #9ca3af !important;
            }

            .text-gray-400 {
                color: #6b7280 !important;
            }

            /* ── Headings inside cards ── */
            h1, h2, h3, h4, h5, h6 {
                color: #f3f4f6;
            }

            /* ── Borders ── */
            .border-gray-200,
            .border-gray-300,
            .divide-gray-200 > :not([hidden]) ~ :not([hidden]) {
                border-color: #374151 !important;
            }

            .border-gray-100 {
                border-color: #1f2937 !important;
            }

            /* ── Tables ── */
            table thead,
            table thead th,
            .bg-gray-50 th {
                background-color: #111827 !important;
                color: #9ca3af !important;
            }

            table tbody td {
                color: #e5e7eb !important;
                border-color: #374151 !important;
            }

            table tbody tr:hover {
                background-color: #374151 !important;
            }

            table tbody tr:nth-child(even) {
                background-color: rgba(55, 65, 81, 0.3) !important;
            }

            /* ── Forms: inputs, selects, textareas ── */
            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="number"],
            input[type="date"],
            input[type="search"],
            input[type="tel"],
            input[type="url"],
            select,
            textarea {
                background-color: #374151 !important;
                color: #f3f4f6 !important;
                border-color: #4b5563 !important;
            }

            input::placeholder,
            textarea::placeholder {
                color: #6b7280 !important;
            }

            input:focus,
            select:focus,
            textarea:focus {
                border-color: #3b82f6 !important;
                background-color: #1f2937 !important;
            }

            /* ── Labels ── */
            label {
                color: #d1d5db !important;
            }

            /* ── Shadows (softer in dark) ── */
            .shadow,
            .shadow-sm,
            .shadow-md {
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.4) !important;
            }

            /* ── Rounded cards / panels ── */
            .rounded-lg.shadow-md,
            .rounded-lg.shadow,
            .p-6.bg-white {
                background-color: #1f2937 !important;
            }

            /* ── Alerts / flash messages ── */
            .bg-green-100 { background-color: #064e3b !important; color: #6ee7b7 !important; border-color: #065f46 !important; }
            .bg-red-100 { background-color: #7f1d1d !important; color: #fca5a5 !important; border-color: #991b1b !important; }
            .bg-yellow-100 { background-color: #78350f !important; color: #fde68a !important; border-color: #92400e !important; }
            .bg-blue-100 { background-color: #1e3a5f !important; color: #93c5fd !important; border-color: #1e40af !important; }

            /* ── Stat cards (colored) — keep as-is but soften text ── */
            .bg-blue-600, .bg-green-600, .bg-yellow-400,
            .bg-cyan-600, .bg-indigo-600, .bg-purple-600,
            .bg-red-500, .bg-orange-500 {
                opacity: 0.92;
            }

            /* ── Badges / pills ── */
            .bg-green-100.text-green-800 { background-color: #064e3b !important; color: #6ee7b7 !important; }
            .bg-red-100.text-red-800,
            .bg-red-100.text-red-600 { background-color: #7f1d1d !important; color: #fca5a5 !important; }
            .bg-blue-100.text-blue-800 { background-color: #1e3a5f !important; color: #93c5fd !important; }
            .bg-purple-100.text-purple-800 { background-color: #3b0764 !important; color: #d8b4fe !important; }
            .bg-yellow-100.text-yellow-800 { background-color: #78350f !important; color: #fde68a !important; }
            .bg-gray-200 { background-color: #374151 !important; color: #d1d5db !important; }

            /* ── Pagination ── */
            nav[role="navigation"] span,
            nav[role="navigation"] a {
                background-color: #1f2937 !important;
                color: #d1d5db !important;
                border-color: #374151 !important;
            }

            nav[role="navigation"] span[aria-current="page"] {
                background-color: #2563eb !important;
                color: #fff !important;
            }

            /* ── Modals ── */
            .modal-content,
            [x-show] > div > div {
                background-color: #1f2937 !important;
                color: #e5e7eb !important;
            }

            /* ── Links inside tables/cards ── */
            a.text-blue-600 { color: #60a5fa !important; }
            a.text-blue-600:hover { color: #93c5fd !important; }
            a.text-yellow-600 { color: #fbbf24 !important; }
            a.text-red-600 { color: #f87171 !important; }
            a.text-green-600,
            a.text-emerald-600 { color: #34d399 !important; }

            /* ── Buttons: keep existing colors, just make borders dark ── */
            .border-gray-300 {
                border-color: #4b5563 !important;
            }

            button.text-gray-700,
            a.text-gray-700 {
                color: #d1d5db !important;
            }

            /* ── Hover backgrounds on filter/action areas ── */
            .hover\:bg-gray-50:hover {
                background-color: #374151 !important;
            }

            /* ── Info/detail cards used in order show, receipt etc ── */
            .order-detail-card,
            .info-grid .info-item,
            .receipt-card {
                background-color: #1f2937 !important;
                color: #e5e7eb !important;
            }

            .info-item label {
                color: #9ca3af !important;
            }

            .info-item p {
                color: #f3f4f6 !important;
            }

            /* ── Status badges on order show ── */
            .status-completed { background: #064e3b !important; color: #6ee7b7 !important; }
            .status-pending { background: #78350f !important; color: #fde68a !important; }
            .status-cancelled { background: #374151 !important; color: #9ca3af !important; }
            .status-refunded { background: #7f1d1d !important; color: #fca5a5 !important; }

            /* ── Filter / search bars ── */
            .bg-gray-50.p-4.rounded-lg {
                background-color: #111827 !important;
                border-color: #374151 !important;
            }

            /* ── Scrollbar (for webkit browsers) ── */
            ::-webkit-scrollbar { width: 8px; height: 8px; }
            ::-webkit-scrollbar-track { background: #1f2937; }
            ::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 4px; }
            ::-webkit-scrollbar-thumb:hover { background: #6b7280; }
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100 font-sans antialiased">
    <div class="min-h-screen flex flex-col md:flex-row" x-data="{ mobileSidebarOpen: false }">

        <!-- Mobile Header with Hamburger Menu - FULL WIDTH -->
        <div
            class="mobile-header md:hidden fixed top-0 left-0 right-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 z-40 w-full">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between h-16">
                    <!-- Left: Burger menu -->
                    <div class="flex items-center flex-shrink-0">
                        <button @click="mobileSidebarOpen = !mobileSidebarOpen"
                            class="text-gray-700 dark:text-gray-300 focus:outline-none mr-3">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>

                    <!-- Center: App Name -->
                    <div class="flex-1 flex justify-center">
                        <div class="text-lg font-semibold text-gray-800 dark:text-gray-200 truncate">
                            {{ is_object($currentBranch ?? null) ? $currentBranch->name : config('app.name', 'Almufeed Saqafti Markaz') }}
                        </div>
                    </div>

                    <!-- Right: Logout button -->
                    <div class="flex items-center flex-shrink-0">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                <i class="fas fa-sign-out-alt text-lg"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay md:hidden" :class="{ 'open': mobileSidebarOpen }" @click="mobileSidebarOpen = false"
            x-show="mobileSidebarOpen" x-transition></div>

        <!-- Sidebar -->
        <!-- Sidebar -->
        <aside class="mobile-sidebar w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700"
            :class="{ 'open': mobileSidebarOpen }">
            <!-- Desktop sidebar title -->
            <div
                class="p-5 text-xl font-semibold border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                Almufeed POS
            </div>

            <!-- Mobile sidebar header (only shows on mobile) -->
            <div
                class="mobile-sidebar-header md:hidden p-5 text-xl font-semibold border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-between items-center">
                <span>Menu</span>
                <button @click="mobileSidebarOpen = false" class="text-gray-700 dark:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Branch Switcher --}}
            @if(isset($currentBranch) && isset($allBranches))
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700" x-data="{ branchOpen: false }">
                    @if(isset($canSwitchBranch) && $canSwitchBranch)
                        {{-- Admin/owner: can switch between branches --}}
                        <button @click="branchOpen = !branchOpen"
                                class="w-full flex items-center justify-between px-3 py-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-sm hover:bg-blue-100 transition">
                            <div class="flex items-center gap-2 min-w-0">
                                <i class="fas fa-store text-blue-600 text-xs flex-shrink-0"></i>
                                <span class="font-medium text-blue-800 dark:text-blue-200 truncate">
                                    {{ $currentBranch === 'all' ? 'All Branches' : $currentBranch->name }}
                                </span>
                            </div>
                            <i class="fas fa-chevron-down text-blue-400 text-[10px] transition-transform flex-shrink-0 ml-1"
                               :class="{ 'rotate-180': branchOpen }"></i>
                        </button>
                        <div x-show="branchOpen" x-transition x-cloak class="mt-2 space-y-1 max-h-48 overflow-y-auto">
                            @foreach($allBranches as $b)
                                @php $isSelected = ($currentBranch !== 'all' && $currentBranch->id === $b->id); @endphp
                                <form method="POST" action="{{ route('admin.branch.store-selection') }}">
                                    @csrf
                                    <input type="hidden" name="branch_id" value="{{ $b->id }}">
                                    <button type="submit"
                                            class="block w-full text-left px-3 py-1.5 rounded-lg text-xs transition
                                                {{ $isSelected ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                                        <i class="fas fa-{{ $isSelected ? 'check-circle' : 'circle' }} mr-1 text-[10px]"></i>
                                        {{ $b->name }}
                                    </button>
                                </form>
                            @endforeach
                            @can('view all branches')
                                <form method="POST" action="{{ route('admin.branch.store-selection') }}">
                                    @csrf
                                    <input type="hidden" name="branch_id" value="all">
                                    <button type="submit"
                                            class="block w-full text-left px-3 py-1.5 rounded-lg text-xs transition
                                                {{ $currentBranch === 'all' ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                                        <i class="fas fa-{{ $currentBranch === 'all' ? 'check-circle' : 'globe' }} mr-1 text-[10px]"></i>
                                        All Branches
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @else
                        {{-- Staff user: locked to their branch, show label only --}}
                        <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-800 rounded-lg text-sm">
                            <i class="fas fa-store text-gray-500 text-xs flex-shrink-0"></i>
                            <span class="font-medium text-gray-700 dark:text-gray-300 truncate">
                                {{ $currentBranch === 'all' ? 'All Branches' : $currentBranch->name }}
                            </span>
                            <i class="fas fa-lock text-gray-400 text-[10px] ml-auto flex-shrink-0" title="You are assigned to this branch"></i>
                        </div>
                    @endif
                </div>
            @endif

            <nav class="p-4 space-y-1 text-sm" x-data="{
                inventoryOpen: false,
                reportsOpen: false,
                attendanceOpen: false,
                hrOpen: false,
                settingsOpen: false,
                storefrontOpen: false
            }">

                {{-- ── Dashboard ── --}}
                <a href="{{ route('admin.dashboard') }}"
                    class="block px-4 py-2 rounded-md transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 text-blue-700 font-semibold dark:bg-blue-900 dark:text-blue-300' : 'hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-700 dark:hover:text-blue-300' }}">
                    <i class="fas fa-tachometer-alt mr-2 text-xs"></i> Dashboard
                </a>

                {{-- ── POS ── --}}
                @can('access pos')
                    <a href="{{ route('admin.pos.index') }}"
                        class="block px-4 py-2 rounded-md transition {{ request()->routeIs('admin.pos.index') ? 'bg-green-100 text-green-700 font-semibold dark:bg-green-900 dark:text-green-300' : 'hover:bg-green-100 dark:hover:bg-green-900 hover:text-green-700 dark:hover:text-green-300' }}">
                        <i class="fas fa-cash-register mr-2 text-xs"></i> POS System
                    </a>
                    <a href="{{ route('admin.pos.returns') }}"
                        class="block px-4 py-2 rounded-md transition {{ request()->routeIs('admin.pos.returns') ? 'bg-red-100 text-red-700 font-semibold dark:bg-red-900 dark:text-red-300' : 'hover:bg-red-100 dark:hover:bg-red-900 hover:text-red-700 dark:hover:text-red-300' }}">
                        <i class="fas fa-undo mr-2 text-xs"></i> Returns
                    </a>
                @endcan

                {{-- ── Customers ── --}}
                <a href="{{ route('admin.customers.index') }}"
                    class="block px-4 py-2 rounded-md transition {{ request()->routeIs('admin.customers.*') ? 'bg-pink-100 text-pink-700 font-semibold dark:bg-pink-900 dark:text-pink-300' : 'hover:bg-pink-100 dark:hover:bg-pink-900 hover:text-pink-700 dark:hover:text-pink-300' }}">
                    <i class="fas fa-users mr-2 text-xs"></i> Customers
                </a>

                {{-- ── Suppliers ── --}}
                @can('manage suppliers')
                    <a href="{{ route('suppliers.index') }}"
                        class="block px-4 py-2 rounded-md transition {{ request()->routeIs('suppliers.*') ? 'bg-orange-100 text-orange-700 font-semibold dark:bg-orange-900 dark:text-orange-300' : 'hover:bg-orange-100 dark:hover:bg-orange-900 hover:text-orange-700 dark:hover:text-orange-300' }}">
                        <i class="fas fa-truck mr-2 text-xs"></i> Suppliers
                    </a>
                @endcan

                {{-- ── Inventory & Products ── --}}
                <button @click="inventoryOpen = !inventoryOpen"
                    class="w-full flex justify-between items-center px-4 py-2 rounded-md transition hover:bg-purple-100 dark:hover:bg-purple-900 hover:text-purple-700 dark:hover:text-purple-300">
                    <span><i class="fas fa-boxes mr-2 text-xs"></i> Inventory</span>
                    <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': inventoryOpen }"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="inventoryOpen" x-transition class="pl-4 space-y-1 text-sm">
                    @can('manage products')
                        <a href="{{ route('products.index') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-purple-100 dark:hover:bg-purple-900 hover:text-purple-700 dark:hover:text-purple-300">
                            <i class="fas fa-box mr-2 text-xs"></i> Products
                        </a>
                        <a href="{{ route('products.create') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-purple-100 dark:hover:bg-purple-900 hover:text-purple-700 dark:hover:text-purple-300">
                            <i class="fas fa-plus mr-2 text-xs"></i> Add Product
                        </a>
                    @endcan
                    @can('manage categories')
                        <a href="{{ route('categories.index') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-purple-100 dark:hover:bg-purple-900 hover:text-purple-700 dark:hover:text-purple-300">
                            <i class="fas fa-tags mr-2 text-xs"></i> Categories
                        </a>
                    @endcan
                    @can('manage products')
                        <a href="{{ route('units.index') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-purple-100 dark:hover:bg-purple-900 hover:text-purple-700 dark:hover:text-purple-300">
                            <i class="fas fa-ruler mr-2 text-xs"></i> Units
                        </a>
                    @endcan
                    <a href="{{ route('admin.packages.index') }}"
                        class="block px-4 py-2 rounded-md transition {{ request()->routeIs('admin.packages.*') ? 'bg-purple-100 text-purple-700 font-semibold' : 'hover:bg-purple-100 dark:hover:bg-purple-900 hover:text-purple-700' }}">
                        <i class="fas fa-box mr-2 text-xs"></i> Packages
                    </a>
                    @can('manage inventory')
                        <a href="{{ route('inventory.index') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-purple-100 dark:hover:bg-purple-900 hover:text-purple-700 dark:hover:text-purple-300">
                            <i class="fas fa-warehouse mr-2 text-xs"></i> Stock Overview
                        </a>
                        <a href="{{ route('inventory.low-stock') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-red-100 dark:hover:bg-red-900 hover:text-red-700 dark:hover:text-red-300">
                            <i class="fas fa-exclamation-triangle mr-2 text-xs"></i> Low Stock
                        </a>
                        <a href="{{ route('inventory.logs') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-purple-100 dark:hover:bg-purple-900 hover:text-purple-700 dark:hover:text-purple-300">
                            <i class="fas fa-history mr-2 text-xs"></i> Stock Logs
                        </a>
                    @endcan
                    @can('manage purchases')
                        <a href="{{ route('purchases.index') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-purple-100 dark:hover:bg-purple-900 hover:text-purple-700 dark:hover:text-purple-300">
                            <i class="fas fa-shopping-cart mr-2 text-xs"></i> Purchases
                        </a>
                    @endcan
                </div>

                {{-- ── Sales Reports ── --}}
                @can('manage reports')
                    <button @click="reportsOpen = !reportsOpen"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-md transition hover:bg-indigo-100 dark:hover:bg-indigo-900 hover:text-indigo-700 dark:hover:text-indigo-300">
                        <span><i class="fas fa-chart-bar mr-2 text-xs"></i> Reports</span>
                        <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': reportsOpen }"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="reportsOpen" x-transition class="pl-4 space-y-1 text-sm">
                        <a href="{{ route('admin.reports.sales') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-indigo-100 dark:hover:bg-indigo-900 hover:text-indigo-700 dark:hover:text-indigo-300">
                            <i class="fas fa-receipt mr-2 text-xs"></i> Sales Report
                        </a>
                        <a href="{{ route('admin.reports.top-products') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-indigo-100 dark:hover:bg-indigo-900 hover:text-indigo-700 dark:hover:text-indigo-300">
                            <i class="fas fa-trophy mr-2 text-xs"></i> Top Products
                        </a>
                        <a href="{{ route('admin.reports.profit-loss') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-indigo-100 dark:hover:bg-indigo-900 hover:text-indigo-700 dark:hover:text-indigo-300">
                            <i class="fas fa-balance-scale mr-2 text-xs"></i> Profit / Loss
                        </a>
                        <a href="{{ route('admin.reports.category-sales') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-indigo-100 dark:hover:bg-indigo-900 hover:text-indigo-700 dark:hover:text-indigo-300">
                            <i class="fas fa-layer-group mr-2 text-xs"></i> Category Sales
                        </a>
                        <a href="{{ route('admin.reports.customer-sales') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-indigo-100 dark:hover:bg-indigo-900 hover:text-indigo-700 dark:hover:text-indigo-300">
                            <i class="fas fa-user-tag mr-2 text-xs"></i> Customer Sales
                        </a>
                        <a href="{{ route('admin.reports.product-statement') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-indigo-100 dark:hover:bg-indigo-900 hover:text-indigo-700 dark:hover:text-indigo-300">
                            <i class="fas fa-file-alt mr-2 text-xs"></i> Product Statement
                        </a>
                    </div>
                @endcan

                {{-- ── Ledger ── --}}
                @can('manage ledger')
                    <a href="{{ route('admin.ledger-accounts.index') }}"
                        class="block px-4 py-2 rounded-md transition {{ request()->routeIs('admin.ledger-accounts.*') ? 'bg-teal-100 text-teal-700 font-semibold dark:bg-teal-900 dark:text-teal-300' : 'hover:bg-teal-100 dark:hover:bg-teal-900 hover:text-teal-700 dark:hover:text-teal-300' }}">
                        <i class="fas fa-book mr-2 text-xs"></i> Ledger Accounts
                    </a>
                @endcan

                {{-- ── Cash In / Out ── --}}
                <a href="{{ route('admin.cash.available') }}"
                    class="block px-4 py-2 rounded-md transition {{ request()->routeIs('admin.cash.available') ? 'bg-emerald-100 text-emerald-700 font-semibold' : 'hover:bg-emerald-100 hover:text-emerald-700' }}">
                    <i class="fas fa-wallet mr-2 text-xs"></i> Available Cash
                </a>
                <a href="{{ route('admin.cash.index') }}"
                    class="block px-4 py-2 rounded-md transition {{ request()->routeIs('admin.cash.index') || request()->routeIs('admin.cash.store') ? 'bg-emerald-100 text-emerald-700 font-semibold dark:bg-emerald-900 dark:text-emerald-300' : 'hover:bg-emerald-100 dark:hover:bg-emerald-900 hover:text-emerald-700 dark:hover:text-emerald-300' }}">
                    <i class="fas fa-money-bill-wave mr-2 text-xs"></i> Cash In / Out
                </a>

                {{--
                    ── Manage Website (storefront: brands / banners / online orders) ──
                    HIDDEN: Feature is complete but not shown until client agreement is finalised.
                    To re-enable, remove this comment block and uncomment the section below.

                @php
                    $websiteOpen = request()->routeIs('admin.brands.*')
                        || request()->routeIs('admin.banners.*')
                        || request()->routeIs('admin.online-orders.*');
                @endphp
                <button @click="storefrontOpen = !storefrontOpen"
                    class="w-full flex items-center justify-between px-4 py-2 rounded-md transition {{ $websiteOpen ? 'bg-cyan-50 text-cyan-700 font-semibold' : 'hover:bg-cyan-100 dark:hover:bg-cyan-900 hover:text-cyan-700 dark:hover:text-cyan-300' }}">
                    <span><i class="fas fa-globe mr-2 text-xs"></i> Manage Website</span>
                    <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': storefrontOpen }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="storefrontOpen || {{ $websiteOpen ? 'true' : 'false' }}" x-cloak class="ml-4 space-y-1">
                    <a href="{{ route('admin.online-orders.index') }}"
                       class="block px-4 py-2 rounded-md transition {{ request()->routeIs('admin.online-orders.*') ? 'bg-cyan-100 text-cyan-700 font-semibold' : 'hover:bg-cyan-50 hover:text-cyan-700' }}">
                        <i class="fas fa-bag-shopping mr-2 text-xs"></i> Online Orders
                        @php
                            try {
                                $newOrders = \App\Models\Order::where('order_source', 'online')->where('status', 'pending')->count();
                            } catch (\Throwable $e) { $newOrders = 0; }
                        @endphp
                        @if ($newOrders > 0)
                            <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 ml-1 rounded-full bg-amber-400 text-[10px] font-bold text-gray-900">{{ $newOrders }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.brands.index') }}"
                       class="block px-4 py-2 rounded-md transition {{ request()->routeIs('admin.brands.*') ? 'bg-cyan-100 text-cyan-700 font-semibold' : 'hover:bg-cyan-50 hover:text-cyan-700' }}">
                        <i class="fas fa-tag mr-2 text-xs"></i> Brands
                    </a>
                    <a href="{{ route('admin.banners.index') }}"
                       class="block px-4 py-2 rounded-md transition {{ request()->routeIs('admin.banners.*') ? 'bg-cyan-100 text-cyan-700 font-semibold' : 'hover:bg-cyan-50 hover:text-cyan-700' }}">
                        <i class="fas fa-image mr-2 text-xs"></i> Banners
                    </a>
                    <a href="{{ url(env('SHOP_PREFIX', 'shop')) }}" target="_blank"
                       class="block px-4 py-2 rounded-md transition hover:bg-cyan-50 hover:text-cyan-700">
                        <i class="fas fa-external-link-alt mr-2 text-xs"></i> View site
                    </a>
                </div>
                --}}

                {{-- ── HR: Employees, Attendance, Payroll ── --}}
                <button @click="hrOpen = !hrOpen"
                    class="w-full flex items-center justify-between px-4 py-2 rounded-md transition hover:bg-cyan-100 dark:hover:bg-cyan-900 hover:text-cyan-700 dark:hover:text-cyan-300">
                    <span><i class="fas fa-user-tie mr-2 text-xs"></i> HR & Payroll</span>
                    <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': hrOpen }"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="hrOpen" x-transition class="pl-4 space-y-1 text-sm">
                    @can('manage employees')
                        <a href="{{ route('employees.index') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-cyan-100 dark:hover:bg-cyan-900 hover:text-cyan-700 dark:hover:text-cyan-300">
                            <i class="fas fa-id-badge mr-2 text-xs"></i> Employees
                        </a>
                    @endcan
                    @can('manage attendance')
                        <a href="{{ route('admin.attendance.index') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-cyan-100 dark:hover:bg-cyan-900 hover:text-cyan-700 dark:hover:text-cyan-300">
                            <i class="fas fa-clipboard-check mr-2 text-xs"></i> Attendance
                        </a>
                        <a href="{{ route('admin.attendance.create') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-cyan-100 dark:hover:bg-cyan-900 hover:text-cyan-700 dark:hover:text-cyan-300">
                            <i class="fas fa-user-check mr-2 text-xs"></i> Mark Attendance
                        </a>
                        <a href="{{ route('admin.attendance.report') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-cyan-100 dark:hover:bg-cyan-900 hover:text-cyan-700 dark:hover:text-cyan-300">
                            <i class="fas fa-calendar-day mr-2 text-xs"></i> Daily Report
                        </a>
                        <a href="{{ route('admin.attendance.monthly-report') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-cyan-100 dark:hover:bg-cyan-900 hover:text-cyan-700 dark:hover:text-cyan-300">
                            <i class="fas fa-calendar-alt mr-2 text-xs"></i> Monthly Report
                        </a>
                        <a href="{{ route('admin.attendance.yearly-report') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-cyan-100 dark:hover:bg-cyan-900 hover:text-cyan-700 dark:hover:text-cyan-300">
                            <i class="fas fa-chart-line mr-2 text-xs"></i> Yearly Report
                        </a>
                    @endcan
                    <a href="{{ route('admin.payroll.index') }}"
                        class="block px-4 py-2 rounded-md transition hover:bg-cyan-100 dark:hover:bg-cyan-900 hover:text-cyan-700 dark:hover:text-cyan-300">
                        <i class="fas fa-money-check-alt mr-2 text-xs"></i> Payroll
                    </a>
                </div>

                {{-- ── Settings & Admin ── --}}
                @hasanyrole('admin|super_admin')
                    <button @click="settingsOpen = !settingsOpen"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-md transition hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300">
                        <span><i class="fas fa-cog mr-2 text-xs"></i> Settings</span>
                        <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': settingsOpen }"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="settingsOpen" x-transition class="pl-4 space-y-1 text-sm">
                        <a href="{{ route('admin.settings.index') }}"
                            class="block px-4 py-2 rounded-md transition hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300">
                            <i class="fas fa-sliders-h mr-2 text-xs"></i> POS Settings
                        </a>
                        @can('manage branches')
                            <a href="{{ route('admin.branches.index') }}"
                                class="block px-4 py-2 rounded-md transition hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300">
                                <i class="fas fa-store mr-2 text-xs"></i> Branches
                            </a>
                        @endcan
                        @can('manage roles')
                            <a href="{{ route('roles.index') }}"
                                class="block px-4 py-2 rounded-md transition hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300">
                                <i class="fas fa-user-shield mr-2 text-xs"></i> Roles
                            </a>
                        @endcan
                        @can('manage permissions')
                            <a href="{{ route('permissions.index') }}"
                                class="block px-4 py-2 rounded-md transition hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300">
                                <i class="fas fa-key mr-2 text-xs"></i> Permissions
                            </a>
                        @endcan
                        @hasrole('super_admin')
                            <a href="{{ route('users.assign_role.form') }}"
                                class="block px-4 py-2 rounded-md transition hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300">
                                <i class="fas fa-user-cog mr-2 text-xs"></i> Assign Roles
                            </a>
                        @endhasrole
                    </div>
                @endhasanyrole

                {{-- ── Logout ── --}}
                <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-100 dark:hover:bg-red-900 hover:text-red-700 dark:hover:text-red-300 rounded-md transition">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 bg-gray-50 dark:bg-gray-950 pt-20 md:pt-6 md:mt-0 main-content-mobile"
            :class="{ 'shifted': mobileSidebarOpen && window.innerWidth < 768 }">
            <header class="mb-6 border-b pb-4 border-gray-200 dark:border-gray-700 hidden md:block">
                <h1 class="text-2xl font-bold leading-tight tracking-tight">
                    @yield('title', 'Dashboard')
                </h1>
                <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                    @csrf
                    <button type="submit"
                        class="flex items-center text-sm text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                                clip-rule="evenodd" />
                        </svg>
                        Logout
                    </button>
                </form>
            </header>

            <section class="main-div space-y-4">
                @yield('content')

            </section>
        </main>

    </div>

    <script>
        // Close mobile sidebar when clicking on a link
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarLinks = document.querySelectorAll('aside nav a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 768) {
                        // Close sidebar on mobile
                        const alpineData = document.querySelector('[x-data]');
                        if (alpineData && alpineData.__x) {
                            alpineData.__x.$data.mobileSidebarOpen = false;
                        }
                    }
                });
            });

            // Close sidebar when clicking on overlay
            const overlay = document.querySelector('.sidebar-overlay');
            if (overlay) {
                overlay.addEventListener('click', () => {
                    const alpineData = document.querySelector('[x-data]');
                    if (alpineData && alpineData.__x) {
                        alpineData.__x.$data.mobileSidebarOpen = false;
                    }
                });
            }

            // Close sidebar when pressing escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    const alpineData = document.querySelector('[x-data]');
                    if (alpineData && alpineData.__x && window.innerWidth < 768) {
                        alpineData.__x.$data.mobileSidebarOpen = false;
                    }
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
