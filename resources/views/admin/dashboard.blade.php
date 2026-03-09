@extends('layouts.admin')

@section('content')
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @hasanyrole('admin|super_admin')
    <div class="space-y-6">

        {{-- ══════════════════════════════════════════
             ROW 1: PRIMARY STAT CARDS
        ══════════════════════════════════════════ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Today's Sales --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Today's Sales</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">Rs. {{ number_format($todaySales, 0) }}</p>
                        <div class="flex items-center mt-2 text-xs">
                            @if($salesChange >= 0)
                                <span class="text-green-600 font-semibold flex items-center gap-1">
                                    <i class="fas fa-arrow-up text-[10px]"></i> {{ $salesChange }}%
                                </span>
                            @else
                                <span class="text-red-500 font-semibold flex items-center gap-1">
                                    <i class="fas fa-arrow-down text-[10px]"></i> {{ abs($salesChange) }}%
                                </span>
                            @endif
                            <span class="text-gray-400 ml-1">vs yesterday</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-blue-500 text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Today's Orders --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Today's Orders</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $todayOrders }}</p>
                        <p class="text-xs text-gray-400 mt-2">Paid: Rs. {{ number_format($todayPaid, 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-green-500 text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Receivables --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Receivables (Wusooli)</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">Rs. {{ number_format($totalReceivables, 0) }}</p>
                        @if($totalAdvances > 0)
                            <p class="text-xs text-blue-500 mt-2">Advances: Rs. {{ number_format($totalAdvances, 0) }}</p>
                        @endif
                    </div>
                    <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-hand-holding-usd text-red-500 text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Stock Value --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Stock Value</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">Rs. {{ number_format($totalStockValue, 0) }}</p>
                        <p class="text-xs text-gray-400 mt-2">{{ $totalProducts }} products</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-warehouse text-purple-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
             ROW 2: MONTHLY OVERVIEW CARDS
        ══════════════════════════════════════════ --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 text-white rounded-xl p-4 shadow-sm">
                <p class="text-xs font-medium text-blue-200 uppercase">This Week</p>
                <p class="text-xl font-bold mt-1">Rs. {{ number_format($weeklySales, 0) }}</p>
            </div>
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 text-white rounded-xl p-4 shadow-sm">
                <p class="text-xs font-medium text-indigo-200 uppercase">This Month</p>
                <p class="text-xl font-bold mt-1">Rs. {{ number_format($monthlySales, 0) }}</p>
                <p class="text-xs text-indigo-200 mt-1">{{ $monthlyOrders }} orders</p>
            </div>
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-xl p-4 shadow-sm">
                <p class="text-xs font-medium text-orange-200 uppercase">Monthly Expenses</p>
                <p class="text-xl font-bold mt-1">Rs. {{ number_format($monthlyExpenses, 0) }}</p>
                <p class="text-xs text-orange-200 mt-1">Today: Rs. {{ number_format($todayExpenses, 0) }}</p>
            </div>
            <div class="bg-gradient-to-br {{ $monthlyProfit >= 0 ? 'from-green-600 to-green-700' : 'from-red-600 to-red-700' }} text-white rounded-xl p-4 shadow-sm">
                <p class="text-xs font-medium {{ $monthlyProfit >= 0 ? 'text-green-200' : 'text-red-200' }} uppercase">Est. Profit</p>
                <p class="text-xl font-bold mt-1">Rs. {{ number_format(abs($monthlyProfit), 0) }}</p>
                <p class="text-xs {{ $monthlyProfit >= 0 ? 'text-green-200' : 'text-red-200' }} mt-1">Cost: Rs. {{ number_format($monthlyCost, 0) }}</p>
            </div>
            <div class="bg-gradient-to-br from-cyan-600 to-cyan-700 text-white rounded-xl p-4 shadow-sm">
                <p class="text-xs font-medium text-cyan-200 uppercase">Employees</p>
                <p class="text-xl font-bold mt-1">{{ $presentEmployees }}/{{ $totalEmployees }}</p>
                <p class="text-xs text-cyan-200 mt-1">Present today</p>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
             ROW 3: ALERTS (Low Stock / Out of Stock)
        ══════════════════════════════════════════ --}}
        @if($lowStockProducts > 0 || $outOfStock > 0)
        <div class="flex flex-wrap gap-3">
            @if($outOfStock > 0)
                <a href="{{ route('inventory.low-stock') }}"
                   class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-red-100 transition">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ $outOfStock }} product(s) out of stock
                </a>
            @endif
            @if($lowStockProducts > 0)
                <a href="{{ route('inventory.low-stock') }}"
                   class="flex items-center gap-2 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-yellow-100 transition">
                    <i class="fas fa-box-open"></i>
                    {{ $lowStockProducts }} product(s) low stock
                </a>
            @endif
        </div>
        @endif

        {{-- ══════════════════════════════════════════
             ROW 4: CHART + TOP DEBTORS
        ══════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Sales Chart (7 days) --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">Last 7 Days Sales</h3>
                    <span class="text-xs text-gray-400">Daily revenue</span>
                </div>
                <div class="relative" style="height: 220px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            {{-- Top Debtors --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user-clock text-red-400"></i> Top Receivables
                </h3>
                @if($topDebtors->count())
                    <div class="space-y-3">
                        @foreach($topDebtors as $debtor)
                            <a href="{{ route('admin.customers.khata', $debtor) }}"
                               class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition border border-gray-50">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $debtor->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $debtor->phone }}</p>
                                </div>
                                <span class="text-sm font-bold text-red-600">Rs. {{ number_format($debtor->current_balance, 0) }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 text-center py-6">No outstanding balances</p>
                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════════════
             ROW 5: RECENT ORDERS + TOP PRODUCTS + PAYMENT METHODS
        ══════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Recent Orders --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">Recent Orders</h3>
                    <a href="{{ route('admin.reports.sales') }}" class="text-xs text-blue-600 hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-gray-500 uppercase border-b">
                                <th class="pb-3 pr-3">Order</th>
                                <th class="pb-3 pr-3">Customer</th>
                                <th class="pb-3 pr-3 text-right">Total</th>
                                <th class="pb-3 pr-3 text-right">Paid</th>
                                <th class="pb-3 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentOrders as $order)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3 pr-3">
                                        <a href="{{ route('admin.pos.receipt', $order) }}" class="text-blue-600 hover:underline font-mono text-xs">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="py-3 pr-3 text-gray-700">{{ Str::limit($order->customer->name ?? 'Walk-in', 20) }}</td>
                                    <td class="py-3 pr-3 text-right font-semibold">Rs. {{ number_format($order->total, 0) }}</td>
                                    <td class="py-3 pr-3 text-right">
                                        @if($order->balance_amount > 0)
                                            <span class="text-red-500 text-xs font-medium">Rs. {{ number_format($order->paid_amount, 0) }}</span>
                                        @else
                                            <span class="text-green-600 text-xs">Paid</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right text-gray-400 text-xs">{{ $order->created_at->format('d M h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Right Column: Top Products + Payment Methods --}}
            <div class="space-y-6">

                {{-- Top Products --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-fire text-orange-400"></i> Top Products
                        <span class="text-xs text-gray-400 font-normal ml-auto">This Month</span>
                    </h3>
                    @if($topProducts->count())
                        <div class="space-y-3">
                            @foreach($topProducts as $i => $item)
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                        {{ $i === 0 ? 'bg-yellow-100 text-yellow-700' : ($i === 1 ? 'bg-gray-100 text-gray-600' : 'bg-orange-50 text-orange-500') }}">
                                        {{ $i + 1 }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate">{{ $item->product->name ?? 'Deleted' }}</p>
                                        <p class="text-xs text-gray-400">{{ number_format($item->total_qty, 1) }} sold</p>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">Rs. {{ number_format($item->total_revenue, 0) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 text-center py-4">No sales this month</p>
                    @endif
                </div>

                {{-- Payment Methods --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-wallet text-indigo-400"></i> Payment Methods
                        <span class="text-xs text-gray-400 font-normal ml-auto">This Month</span>
                    </h3>
                    @if($paymentBreakdown->count())
                        <div class="space-y-2">
                            @php $maxPayment = $paymentBreakdown->max('total') ?: 1; @endphp
                            @foreach($paymentBreakdown as $pm)
                                <div>
                                    <div class="flex items-center justify-between text-sm mb-1">
                                        <span class="text-gray-700 capitalize">{{ str_replace('_', ' ', $pm->payment_method) }}</span>
                                        <span class="font-semibold text-gray-800">Rs. {{ number_format($pm->total, 0) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ ($pm->total / $maxPayment) * 100 }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $pm->count }} orders</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 text-center py-4">No data</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
             ROW 6: ATTENDANCE
        ══════════════════════════════════════════ --}}
        @if($employeeAttendance->count())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-user-check text-cyan-500"></i> Today's Attendance
                </h3>
                <span class="text-xs text-gray-400">{{ now()->format('l, d M Y') }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($employeeAttendance as $attendance)
                    <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 {{ $attendance->status === 'present' ? 'bg-green-50/50' : 'bg-red-50/50' }}">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold
                            {{ $attendance->status === 'present' ? 'bg-green-200 text-green-700' : 'bg-red-200 text-red-700' }}">
                            {{ strtoupper(substr($attendance->employee->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $attendance->employee->user->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">
                                @if($attendance->check_in)
                                    {{ $attendance->check_in }}@if($attendance->check_out) — {{ $attendance->check_out }}@endif
                                @else
                                    {{ ucfirst($attendance->status) }}
                                @endif
                            </p>
                        </div>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full
                            {{ $attendance->status === 'present' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($attendance->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- ══════════════════════════════════════════
         CHART.JS
    ══════════════════════════════════════════ --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesChart');
            if (!ctx) return;

            const data = @json($salesChart);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.date),
                    datasets: [{
                        label: 'Sales (Rs.)',
                        data: data.map(d => d.total),
                        backgroundColor: 'rgba(59, 130, 246, 0.15)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 2,
                        borderRadius: 6,
                        barPercentage: 0.6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => {
                                    const idx = ctx.dataIndex;
                                    return `Rs. ${ctx.parsed.y.toLocaleString()} (${data[idx].orders} orders)`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => v >= 1000 ? (v/1000) + 'k' : v,
                                font: { size: 11 }
                            },
                            grid: { color: 'rgba(0,0,0,0.04)' }
                        },
                        x: {
                            ticks: { font: { size: 11 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
    @endhasanyrole
@endsection
