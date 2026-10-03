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
        $todaySales     = $this->sales(Order::whereDate('created_at', $today))->sum('total');
        $yesterdaySales = $this->sales(Order::whereDate('created_at', $yesterday))->sum('total');
        $todayOrders    = $this->sales(Order::whereDate('created_at', $today))->count();
        $todayPaid      = $this->sales(Order::whereDate('created_at', $today))->sum('paid_amount');

        // ── Weekly & Monthly ──
        $weeklySales   = $this->sales(Order::where('created_at', '>=', $weekStart))->sum('total');
        $monthlySales  = $this->sales(Order::where('created_at', '>=', $monthStart))->sum('total');
        $monthlyOrders = $this->sales(Order::where('created_at', '>=', $monthStart))->count();

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
        // Receivables (wasooli): what customers owe on their khata, plus unpaid
        // bills that have no customer account (walk-in / website guest).
        $totalReceivables = $this->receivablesTotal();
        $totalAdvances = $this->scopeBranch(Customer::query())->where('current_balance', '<', 0)->sum(DB::raw('ABS(current_balance)'));
        // Expenses from ledger accounts of type 'expense' (debit = expense incurred)
        $expenseAccountIds = \App\Models\LedgerAccount::where('type', 'expense')->pluck('id');
        $monthlyExpenses = \App\Models\LedgerAccountEntry::whereIn('ledger_account_id', $expenseAccountIds)
            ->where('entry_date', '>=', $monthStart)
            ->sum('debit');
        $todayExpenses = \App\Models\LedgerAccountEntry::whereIn('ledger_account_id', $expenseAccountIds)
            ->whereDate('entry_date', $today)
            ->sum('debit');

        // ── Profit estimate (this month) ──
        // profit = item sales revenue - COGS - expenses - delivery (delivery is pass-through to courier)
        // order.total includes delivery, so we subtract it back out
        // Counted sales only (POS not cancelled/returned/refunded; online once delivered).
        // Profit = sales − tax (govt.) − delivery (courier) − cost − partial refunds − expenses.
        // Cost uses the cost saved at sale time, else today's product cost.
        $monthlyCost             = $this->costOf(fn ($q) => $q->where('created_at', '>=', $monthStart));
        $monthlyDeliveryCharges  = $this->sales(Order::where('created_at', '>=', $monthStart))->sum('delivery_charges');
        $monthlyTax              = $this->sales(Order::where('created_at', '>=', $monthStart))->sum('tax');
        $monthlyRefunds          = $this->refundsOf(fn ($q) => $q->where('refunds.created_at', '>=', $monthStart));
        $monthlyProfit           = $monthlySales - $monthlyTax - $monthlyDeliveryCharges - $monthlyCost - $monthlyRefunds - $monthlyExpenses;

        // ── Employees ──
        $presentEmployees = $this->scopeBranch(Attendance::whereDate('date', $today)->where('status', 'present'))->count();
        $totalEmployees   = $this->scopeBranch(Employee::query())->count();

        // ── Top 5 products (this month) ──
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_price) as total_revenue'))
            ->whereHas('order', fn ($q) => $this->sales($q->where('created_at', '>=', $monthStart)))
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
        $chartQuery = $this->sales(
            Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as orders')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
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
        $paymentBreakdown = $this->sales(
            Order::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
                ->where('created_at', '>=', $monthStart)
        )->groupBy('payment_method')->get();

        // Daily stats for today
        $todayCost     = $this->costOf(fn ($q) => $q->whereDate('created_at', $today));
        $todayDelivery = $this->sales(Order::whereDate('created_at', $today))->sum('delivery_charges');
        $todayTax      = $this->sales(Order::whereDate('created_at', $today))->sum('tax');
        $todayRefunds  = $this->refundsOf(fn ($q) => $q->whereDate('refunds.created_at', $today));
        $todayProfit   = $todaySales - $todayTax - $todayDelivery - $todayCost - $todayRefunds - $todayExpenses;

        $todayPurchases = Purchase::whereDate('purchase_date', $today)
            ->when(!$this->isAllBranches(), fn($q) => $q->where('branch_id', $this->branchId()))
            ->sum('total_amount');

        // ── Online (storefront) orders snapshot ──
        $onlineBase = $this->scopeBranch(Order::query())->where('order_source', 'online');
        $onlineOrders = [
            'all'       => (clone $onlineBase)->count(),
            'pending'   => (clone $onlineBase)->where('status', 'pending')->count(),
            'confirmed' => (clone $onlineBase)->where('status', 'confirmed')->count(),
            'shipped'   => (clone $onlineBase)->where('status', 'shipped')->count(),
            'delivered' => (clone $onlineBase)->whereIn('status', ['delivered', 'completed'])->count(),
            'cancelled' => (clone $onlineBase)->where('status', 'cancelled')->count(),
            'unpaid'    => (clone $onlineBase)->where('online_payment_status', 'bank_pending')->count(),
        ];
        // "Remaining" = received but not yet delivered/cancelled
        $onlineOrders['remaining'] = $onlineOrders['pending'] + $onlineOrders['confirmed'] + $onlineOrders['shipped'];

        return view('admin.dashboard', compact(
            'todaySales', 'yesterdaySales', 'salesChange',
            'todayOrders', 'todayPaid', 'todayExpenses',
            'todayProfit', 'todayCost', 'todayPurchases',
            'weeklySales', 'monthlySales', 'monthlyOrders',
            'monthlyExpenses', 'monthlyProfit', 'monthlyCost',
            'lowStockProducts', 'outOfStock', 'totalProducts', 'totalStockValue',
            'totalReceivables', 'totalAdvances',
            'presentEmployees', 'totalEmployees',
            'topProducts', 'topDebtors',
            'recentOrders', 'employeeAttendance',
            'salesChart', 'paymentBreakdown', 'onlineOrders'
        ));
    }

    // Customers who owe us money (receivables / wusooli)
    public function receivables()
    {
        // Named customers: their khata balance (what the statement says they owe),
        // with the bills that are still unpaid after khata payments are applied.
        $customers = $this->scopeBranch(Customer::query())
            ->where('current_balance', '>', 0)
            ->orderByDesc('current_balance')
            ->get();

        $unpaid = Order::whereIn('customer_id', $customers->pluck('id'))
            ->whereNotIn('status', self::NOT_RECEIVABLE)
            ->where('balance_amount', '>', 0)
            ->latest()->get()->groupBy('customer_id');

        $customerRows = $customers->map(fn ($c) => [
            'customer'    => $c,
            'orders'      => $unpaid->get($c->id, collect()),
            'total_due'   => (float) $c->current_balance,
            'order_count' => $unpaid->get($c->id, collect())->count(),
        ])->all();

        // Bills with no customer account (walk-in / website guest) still unpaid.
        $walkinOrders = $this->scopeBranch(Order::with('customer'))
            ->whereNull('customer_id')
            ->whereNotIn('status', self::NOT_RECEIVABLE)
            ->where('balance_amount', '>', 0)
            ->latest()->get();

        $total = $customers->sum('current_balance') + $walkinOrders->sum('balance_amount');

        return view('admin.dashboard-detail.receivables', compact('customerRows', 'walkinOrders', 'total'));
    }

    // Customers who have advance with us (we owe them)
    public function advances()
    {
        $customers = $this->scopeBranch(Customer::query())
            ->where('current_balance', '<', 0)
            ->orderBy('current_balance')
            ->get();

        $total = abs($customers->sum('current_balance'));

        return view('admin.dashboard-detail.advances', compact('customers', 'total'));
    }

    /** Order statuses whose unpaid balance is not money to collect. */
    private const NOT_RECEIVABLE = ['cancelled', 'returned', 'refunded'];

    /**
     * Restrict an order query to counted sales in the current branch scope:
     * POS bills that were not cancelled / returned / fully refunded, and website
     * orders only once delivered (same point the ledger records the sale).
     */
    private function sales($query)
    {
        return $this->scopeBranch($query)->where(function ($w) {
            $w->where(function ($pos) {
                $pos->where(fn ($x) => $x->where('order_source', '!=', 'online')->orWhereNull('order_source'))
                    ->whereNotIn('status', ['cancelled', 'returned', 'refunded']);
            })->orWhere(function ($online) {
                $online->where('order_source', 'online')->whereIn('status', ['delivered', 'completed']);
            });
        });
    }

    /** Cost of goods for counted sales matching $period (cost at sale time, else today's). */
    private function costOf(\Closure $period): float
    {
        return (float) (OrderItem::whereHas('order', fn ($q) => $this->sales($period($q)))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('SUM(order_items.quantity * COALESCE(order_items.cost_price, products.cost_price, 0)) as cost')
            ->value('cost') ?? 0);
    }

    /** Partial refunds (on bills still counted as sales) made in $period. */
    private function refundsOf(\Closure $period): float
    {
        $q = \App\Models\Refund::query()
            ->join('orders', 'orders.id', '=', 'refunds.order_id')
            ->where('refunds.status', 'completed')
            ->whereNotIn('orders.status', ['cancelled', 'returned', 'refunded']);
        $period($q);
        $this->scopeBranch($q, 'orders.branch_id');
        return (float) $q->sum('refunds.amount');
    }

    /** Wasooli: positive khata balances + unpaid bills without a customer account. */
    private function receivablesTotal(): float
    {
        $khata = (float) $this->scopeBranch(Customer::query())->where('current_balance', '>', 0)->sum('current_balance');
        $guest = (float) $this->scopeBranch(Order::query())
            ->whereNull('customer_id')
            ->whereNotIn('status', self::NOT_RECEIVABLE)
            ->where('balance_amount', '>', 0)
            ->sum('balance_amount');
        return round($khata + $guest, 2);
    }
}
