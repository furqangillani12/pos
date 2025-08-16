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

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100 font-sans antialiased">
<div class="min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
        <div class="p-5 text-xl font-semibold border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            Almufeed POS
        </div>

        <nav class="p-4 space-y-1 text-sm" x-data="{
            attendanceOpen: false,
            reportsOpen: false,
            inventoryOpen: false,
            productsOpen: false,
            categoriesOpen: false
        }">

            <a href="{{ route('admin.dashboard') }}"
               class="block px-4 py-2 rounded-md transition hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-700 dark:hover:text-blue-300">
                🧭 Dashboard
            </a>
            @can('manage employees')
                <a href="{{ route('employees.index') }}"
                   class="block px-4 py-2 rounded-md transition hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-700 dark:hover:text-blue-300">

                    👨‍💼 Employees
                </a>
            @endcan

            <!-- Add this right after the Employees link -->
            @can('manage suppliers')
            <a href="{{ route('suppliers.index') }}"
               class="block px-4 py-2 rounded-md transition hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-700 dark:hover:text-blue-300">
                🏭 Suppliers
            </a>
            @endcan
            <!-- Inventory Management Toggle -->

            <button @click="inventoryOpen = !inventoryOpen"
                    class="w-full flex justify-between items-center px-4 py-2 rounded-md transition hover:bg-purple-100 dark:hover:bg-purple-900 hover:text-purple-700 dark:hover:text-purple-300">
                <div class="flex items-center space-x-2">
                    <span>📦</span>
                    <span>Inventory Management</span>
                </div>
                <svg class="w-4 h-4 transform transition-transform"
                     :class="{ 'rotate-180': inventoryOpen }" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 9l-7 7-7-7"/>
                </svg>
            </button>



            <div x-show="inventoryOpen" x-transition class="pl-4 space-y-1 text-sm">
                <!-- Products Section -->
                @can('manage products')
                <button @click="productsOpen = !productsOpen"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-md transition hover:bg-green-100 dark:hover:bg-green-900 hover:text-green-700 dark:hover:text-green-300">
                    🛍️ Products
                    <svg class="w-4 h-4 ml-2 transform transition-transform"
                         :class="{ 'rotate-180': productsOpen }" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="productsOpen" x-transition class="pl-4 space-y-1">
                    <a href="{{ route('products.index') }}"
                       class="block px-4 py-2 rounded-md transition hover:bg-green-100 dark:hover:bg-green-900 hover:text-green-700 dark:hover:text-green-300">
                        📋 All Products
                    </a>
                    <a href="{{ route('products.create') }}"
                       class="block px-4 py-2 rounded-md transition hover:bg-green-100 dark:hover:bg-green-900 hover:text-green-700 dark:hover:text-green-300">
                        ➕ Add Product
                    </a>
                    <a href="{{ route('products.import.show') }}"
                       class="block px-4 py-2 rounded-md transition hover:bg-green-100 dark:hover:bg-green-900 hover:text-green-700 dark:hover:text-green-300">
                        📤 Import Products
                    </a>
                </div>
                @endcan

                @can('manage categories')
                <!-- Categories Section -->
                <button @click="categoriesOpen = !categoriesOpen"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-md transition hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-700 dark:hover:text-blue-300">
                    🏷️ Categories
                    <svg class="w-4 h-4 ml-2 transform transition-transform"
                         :class="{ 'rotate-180': categoriesOpen }" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>


                <div x-show="categoriesOpen" x-transition class="pl-4 space-y-1">
                    <a href="{{ route('categories.index') }}"
                       class="block px-4 py-2 rounded-md transition hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-700 dark:hover:text-blue-300">
                        📋 All Categories
                    </a>
                    <a href="{{ route('categories.create') }}"
                       class="block px-4 py-2 rounded-md transition hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-700 dark:hover:text-blue-300">
                        ➕ Add Category
                    </a>
                </div>
                @endcan

                @can('manage inventory')
                <!-- Inventory Control Section -->
                <a href="{{ route('inventory.index') }}"
                   class="block px-4 py-2 rounded-md transition hover:bg-yellow-100 dark:hover:bg-yellow-900 hover:text-yellow-700 dark:hover:text-yellow-300">
                    📊 Inventory Overview
                </a>
                <a href="{{ route('inventory.low-stock') }}"
                   class="block px-4 py-2 rounded-md transition hover:bg-red-100 dark:hover:bg-red-900 hover:text-red-700 dark:hover:text-red-300">
                    ⚠️ Low Stock Alerts
                </a>
                <a href="{{ route('inventory.logs') }}"
                   class="block px-4 py-2 rounded-md transition hover:bg-indigo-100 dark:hover:bg-indigo-900 hover:text-indigo-700 dark:hover:text-indigo-300">
                    📝 Inventory Logs
                </a>
                @endcan
                <!-- Add this with your other navigation links -->

                @can('manage purchases')
                <a href="{{ route('purchases.index') }}"
                   class="block px-4 py-2 rounded-md transition hover:bg-purple-100 dark:hover:bg-purple-900 hover:text-purple-700 dark:hover:text-purple-300">
                    🛒 Purchase Orders
                </a>
                @endcan
            </div>
            <!-- Add this right after the Inventory Management section -->
            @can('access pos')
            <a href="{{ route('admin.pos.index') }}"
               class="block px-4 py-2 rounded-md transition hover:bg-green-100 dark:hover:bg-green-900 hover:text-green-700 dark:hover:text-green-300">
                💰 POS System
            </a>
            @endcan
            <!-- Attendance Toggle -->
            @can('manage attendance')
            <button @click="attendanceOpen = !attendanceOpen"
                    class="w-full flex items-center justify-between px-4 py-2 rounded-md transition hover:bg-green-100 dark:hover:bg-green-900 hover:text-green-700 dark:hover:text-green-300">
                📋 Attendance
                <svg class="w-4 h-4 ml-2 transform transition-transform"
                     :class="{ 'rotate-180': attendanceOpen }" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="attendanceOpen" x-transition class="pl-4 space-y-1 text-sm">
                <a href="{{ route('admin.attendance.index') }}"
                   class="block px-4 py-2 rounded-md transition hover:bg-green-100 dark:hover:bg-green-900 hover:text-green-700 dark:hover:text-green-300">
                    📄 View Attendance
                </a>

                <a href="{{ route('admin.attendance.create') }}"
                   class="block px-4 py-2 rounded-md transition hover:bg-yellow-100 dark:hover:bg-yellow-900 hover:text-yellow-700 dark:hover:text-yellow-300">
                    📝 Mark Attendance
                </a>
                <a href="{{ route('admin.attendance.bulk-create') }}"
                   class="block px-4 py-2 rounded-md transition hover:bg-yellow-100 dark:hover:bg-yellow-900 hover:text-yellow-700 dark:hover:text-yellow-300">
                    📝 Mark Bulk Attendance
                </a>

                <!-- Reports Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-2 rounded-md transition hover:bg-indigo-100 dark:hover:bg-indigo-900 hover:text-indigo-700 dark:hover:text-indigo-300">
                        📊 Reports
                        <svg class="w-4 h-4 ml-2 transform transition-transform"
                             :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition
                         class="absolute z-10 mt-2 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-md">
                        <a href="{{ route('admin.attendance.report') }}"
                           class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                            📅 Daily Report
                        </a>
                        <a href="{{ route('admin.attendance.monthly-report') }}"
                           class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                            📆 Monthly Report
                        </a>
                        <a href="{{ route('admin.attendance.yearly-report') }}"
                           class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                            📈 Yearly Report
                        </a>
                    </div>
                </div>
            </div>
            @endcan
            <!-- Sales Reports Section -->

            @can('manage reports')
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-md transition hover:bg-indigo-100 dark:hover:bg-indigo-900 hover:text-indigo-700 dark:hover:text-indigo-300">
                    📊 Sales Reports
                    <svg class="w-4 h-4 ml-2 transform transition-transform"
                         :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition
                     class="pl-4 mt-2 space-y-1 text-sm">
                    <a href="{{ route('admin.reports.sales') }}"
                       class="block px-4 py-2 rounded-md transition hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-700 dark:hover:text-blue-300">
                        📅 Sales Reports
                    </a>
                    <a href="{{ route('admin.reports.top-products') }}"
                       class="block px-4 py-2 rounded-md transition hover:bg-pink-100 dark:hover:bg-pink-900 hover:text-pink-700 dark:hover:text-pink-300">
                        🥇 Top Products
                    </a>
                    <a href="{{ route('admin.reports.profit-loss') }}"
                       class="block px-4 py-2 rounded-md transition hover:bg-yellow-100 dark:hover:bg-yellow-900 hover:text-yellow-700 dark:hover:text-yellow-300">
                        💸 Profit/Loss
                    </a>
                    <a href="{{ route('admin.reports.category-sales') }}"
                       class="block px-4 py-2 rounded-md transition hover:bg-green-100 dark:hover:bg-green-900 hover:text-green-700 dark:hover:text-green-300">
                        🏷️ Category Sales
                    </a>
                    <a href="{{ route('admin.reports.customer-sales') }}"
                       class="block px-4 py-2 rounded-md transition hover:bg-purple-100 dark:hover:bg-purple-900 hover:text-purple-700 dark:hover:text-purple-300">
                        👥 Customer Sales
                    </a>
                </div>
            </div>
            @endcan
            <!-- Roles & Permissions Section -->


            <div x-data="{ openRoles: false }" class="relative">

                <button @click="openRoles = !openRoles"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-md transition hover:bg-indigo-100 dark:hover:bg-indigo-900 hover:text-indigo-700 dark:hover:text-indigo-300">
                    🔐 Roles & Permissions
                    <svg class="w-4 h-4 ml-2 transform transition-transform"
                         :class="{ 'rotate-180': openRoles }" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="openRoles" x-transition class="pl-4 mt-2 space-y-1 text-sm">
                    @can('manage roles')
                    <a href="{{ route('roles.index') }}"
                       class="block px-4 py-2 rounded-md transition hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-700 dark:hover:text-blue-300">
                        👥 Manage Roles
                    </a>
                    @endcan

                        @can('manage permissions')
                    <a href="{{ route('permissions.index') }}"
                       class="block px-4 py-2 rounded-md transition hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-700 dark:hover:text-blue-300">
                        🛡️ Manage Permissions
                    </a>
                        @endcan
                        @can('assign roles')
                    <a href="{{ route('users.assign_role.form') }}"
                       class="block px-4 py-2 rounded-md transition hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-700 dark:hover:text-blue-300">
                        🔗 Assign Roles
                    </a>
                        @endcan
                </div>

            </div>


            <!-- Add this above the closing </nav> tag -->
            <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-100 dark:hover:bg-red-900 hover:text-red-700 dark:hover:text-red-300 rounded-md transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 bg-gray-50 dark:bg-gray-950">
        <header class="mb-6 border-b pb-4 border-gray-200 dark:border-gray-700">
            <h1 class="text-2xl font-bold leading-tight tracking-tight">
                @yield('title', 'Dashboard')
            </h1>
            <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                @csrf
                <button type="submit"
                        class="flex items-center text-sm text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                    </svg>
                    Logout
                </button>
            </form>
        </header>

        <section class="space-y-4">
            @yield('content')
        </section>
    </main>

</div>
</body>
</html>
