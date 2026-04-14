<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\OrderItem;
use App\Models\BranchProductStock;
use App\Traits\BranchScoped;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use BranchScoped;

    public function index()
    {
        $today      = Carbon::today();
        $yesterday  = Carbon::yesterday();
        $weekStart  = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        // ── Today's stats ──
        $todaySales     = $this->scopeBranch(Order::whereDate('created_at', $today)->where('status', '!=', 'cancelled'))->sum('total');
        $yesterdaySales = $this->scopeBranch(Order::whereDate('created_at', $yesterday)->where('status', '!=', 'cancelled'))->sum('total');
        $todayOrders    = $this->scopeBranch(Order::whereDate('created_at', $today)->where('status', '!=', 'cancelled'))->count();
        $todayPaid      = $this->scopeBranch(Order::whereDate('created_at', $today)->where('status', '!=', 'cancelled'))->sum('paid_amount');

        // ── Weekly & Monthly ──
        $weeklySales   = $this->scopeBranch(Order::where('created_at', '>=', $weekStart)->where('status', '!=', 'cancelled'))->sum('total');
        $monthlySales  = $this->scopeBranch(Order::where('created_at', '>=', $monthStart)->where('status', '!=', 'cancelled'))->sum('total');
        $monthlyOrders = $this->scopeBranch(Order::where('created_at', '>=', $monthStart)->where('status', '!=', 'cancelled'))->count();

        // ── Sales trend ──
        $salesChange = $yesterdaySales > 0
            ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1)
            : ($todaySales > 0 ? 100 : 0);

        // ── Inventory (branch stock) ──
        $branchId = $this->branchId();
        if ($this->isAllBranches()) {
            // All branches: use global product stock
            $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'reorder_level')->count();
            $outOfStock       = Product::where('stock_quantity', '<=', 0)->count();
            $totalProducts    = Product::count();
            $totalStockValue  = Product::selectRaw('SUM(stock_quantity * COALESCE(cost_price, 0)) as value')->value('value') ?? 0;
        } else {
            // Specific branch: only count products that belong to this branch
            $branchProductIds = Product::where('branch_id', $branchId)->pluck('id');

            $lowStockProducts = BranchProductStock::where('branch_id', $branchId)
                ->whereIn('product_id', $branchProductIds)
                ->whereColumn('stock_quantity', '<=', 'reorder_level')->count();
            $outOfStock = BranchProductStock::where('branch_id', $branchId)
                ->whereIn('product_id', $branchProductIds)
                ->where('stock_quantity', '<=', 0)->count();
            $totalProducts = $branchProductIds->count();
            $totalStockValue = BranchProductStock::where('branch_product_stock.branch_id', $branchId)
                ->whereIn('branch_product_stock.product_id', $branchProductIds)
                ->join('products', 'branch_product_stock.product_id', '=', 'products.id')
                ->selectRaw('SUM(branch_product_stock.stock_quantity * COALESCE(products.cost_price, 0)) as value')
                ->value('value') ?? 0;
        }

        // ── Financial ──
        $totalReceivables = $this->scopeBranch(Customer::query())->where('current_balance', '>', 0)->sum('current_balance');
        $totalAdvances    = $this->scopeBranch(Customer::query())->where('current_balance', '<', 0)->sum(DB::raw('ABS(current_balance)'));
        $monthlyExpenses  = Expense::where('date', '>=', $monthStart)->sum('amount');
        $todayExpenses    = Expense::whereDate('date', $today)->sum('amount');

        // ── Profit estimate (this month) ──
        $monthlyCostQuery = OrderItem::whereHas('order', function ($q) use ($monthStart) {
            $q->where('created_at', '>=', $monthStart)->where('status', '!=', 'cancelled');
            $this->scopeBranch($q);
        })->join('products', 'order_items.product_id', '=', 'products.id')
          ->selectRaw('SUM(order_items.quantity * COALESCE(products.cost_price, 0)) as cost');
        $monthlyCost   = $monthlyCostQuery->value('cost') ?? 0;
        // Exclude delivery charges and tax from profit — profit = sales revenue - cost - expenses - non-product charges
        $monthlyOrdersQuery = $this->scopeBranch(Order::where('created_at', '>=', $monthStart)->where('status', '!=', 'cancelled'));
        $monthlyDeliveryCharges = (clone $monthlyOrdersQuery)->sum('delivery_charges');
        $monthlyTax = (clone $monthlyOrdersQuery)->sum('tax');
        $monthlyDiscount = (clone $monthlyOrdersQuery)->sum('discount');
        $monthlyProfit = $monthlySales - $monthlyCost - $monthlyExpenses - $monthlyDeliveryCharges - $monthlyTax + $monthlyDiscount;

        // ── Employees ──
        $presentEmployees = $this->scopeBranch(Attendance::whereDate('date', $today)->where('status', 'present'))->count();
        $totalEmployees   = $this->scopeBranch(Employee::query())->count();

        // ── Top 5 products (this month) ──
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_price) as total_revenue'))
            ->whereHas('order', function ($q) use ($monthStart) {
                $q->where('created_at', '>=', $monthStart)->where('status', '!=', 'cancelled');
                $this->scopeBranch($q);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->with('product:id,name')
            ->take(5)
            ->get();

        // ── Top 5 debtors ──
        $topDebtors = $this->scopeBranch(Customer::query())->where('current_balance', '>', 0)
            ->orderByDesc('current_balance')
            ->take(5)
            ->get(['id', 'name', 'phone', 'current_balance', 'branch_id']);

        // ── Recent orders ──
        $recentOrders = $this->scopeBranch(Order::with('customer')->where('status', '!=', 'cancelled'))
            ->latest()
            ->take(7)
            ->get();

        // ── Attendance ──
        $employeeAttendance = $this->scopeBranch(Attendance::with('employee.user')->whereDate('date', $today))->get();

        // ── Last 7 days sales chart ──
        $chartQuery = $this->scopeBranch(
            Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as orders')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->where('status', '!=', 'cancelled')
        )->groupBy('date')->orderBy('date');
        $chartData = $chartQuery->get();

        $salesChart = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date    = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayData = $chartData->firstWhere('date', $date);
            $salesChart->push([
                'date'   => Carbon::parse($date)->format('D'),
                'total'  => (float) ($dayData->total ?? 0),
                'orders' => (int) ($dayData->orders ?? 0),
            ]);
        }

        // ── Payment breakdown ──
        $paymentBreakdown = $this->scopeBranch(
            Order::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
                ->where('created_at', '>=', $monthStart)
                ->where('status', '!=', 'cancelled')
        )->groupBy('payment_method')->get();

        return view('admin.dashboard', compact(
            'todaySales', 'yesterdaySales', 'salesChange',
            'todayOrders', 'todayPaid', 'todayExpenses',
            'weeklySales', 'monthlySales', 'monthlyOrders',
            'monthlyExpenses', 'monthlyProfit', 'monthlyCost',
            'lowStockProducts', 'outOfStock', 'totalProducts', 'totalStockValue',
            'totalReceivables', 'totalAdvances',
            'presentEmployees', 'totalEmployees',
            'topProducts', 'topDebtors',
            'recentOrders', 'employeeAttendance',
            'salesChart', 'paymentBreakdown'
        ));
    }
}
