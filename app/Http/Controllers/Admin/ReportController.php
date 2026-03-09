<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;
use App\Traits\BranchScoped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use BranchScoped;

    public function sales(Request $request)
    {
        $start       = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end         = $request->input('end_date', now()->endOfMonth()->toDateString());
        $orderNumber = $request->input('order_number');

        $query = $this->scopeBranch(Order::query());

        if (!empty($orderNumber)) {
            $query->where('order_number', 'like', "%{$orderNumber}%");
        } else {
            $query->whereBetween('created_at', [$start, $end]);
        }

        $orders     = $query->get();
        $totalSales  = $orders->sum('total');
        $totalOrders = $orders->count();

        return view('admin.reports.sales', compact('orders', 'totalSales', 'totalOrders', 'start', 'end', 'orderNumber'));
    }

    public function topProducts(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end   = $request->input('end_date', now()->endOfMonth()->toDateString());

        $topProducts = OrderItem::selectRaw('product_id, SUM(quantity) as total_quantity, SUM(total_price) as total_revenue')
            ->whereBetween('created_at', [$start, $end])
            ->whereHas('order', function ($q) {
                $this->scopeBranch($q);
            })
            ->groupBy('product_id')
            ->with('product')
            ->get()
            ->map(function ($item) {
                $item->name = isset($item->product->name) ? $item->product->name : 'N/A';
                return $item;
            });

        return view('admin.reports.top_products', compact('topProducts', 'start', 'end'));
    }

    public function profitLoss(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end   = $request->input('end_date', now()->endOfMonth()->toDateString());

        $orders = $this->scopeBranch(Order::with('items.product'))
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $totalRevenue = 0;
        $totalCost    = 0;

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $product       = $item->product;
                $revenue       = $item->quantity * $item->unit_price;
                $cost          = $product ? $item->quantity * ($product->cost_price ?? 0) : 0;
                $totalRevenue += $revenue;
                $totalCost    += $cost;
            }
        }

        $profit = $totalRevenue > $totalCost ? $totalRevenue - $totalCost : 0;
        $loss   = $totalCost > $totalRevenue ? $totalCost - $totalRevenue : 0;

        return view('admin.reports.profit_loss', compact('start', 'end', 'profit', 'loss', 'totalRevenue', 'totalCost'));
    }

    public function categorySales(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end   = $request->input('end_date', now()->endOfMonth()->toDateString());

        $query = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$start, $end]);

        if (!$this->isAllBranches()) {
            $query->where('orders.branch_id', $this->branchId());
        }

        $salesByCategory = $query->select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
            )
            ->groupBy('categories.name')
            ->get();

        return view('admin.reports.category_sales', compact('salesByCategory', 'start', 'end'));
    }

    public function customerSales(Request $request)
    {
        $start        = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end          = $request->input('end_date', now()->endOfMonth()->toDateString());
        $customerName = $request->input('customer_name');

        $query = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->when($customerName, function ($query, $customerName) {
                $query->where('customers.name', 'like', "%{$customerName}%");
            });

        if (!$this->isAllBranches()) {
            $query->where('orders.branch_id', $this->branchId());
        }

        $customerSales = $query->select(
                'customers.id as customer_id',
                'customers.name as customer_name',
                'customers.email',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total) as total_spent'),
                DB::raw('MAX(orders.created_at) as last_order_date')
            )
            ->groupBy('customers.id', 'customers.name', 'customers.email')
            ->get();

        return view('admin.reports.customer_sales', compact('customerSales', 'start', 'end', 'customerName'));
    }

    public function getCustomerOrders($customerId)
    {
        $customer = \App\Models\Customer::findOrFail($customerId);

        $orders = $this->scopeBranch(Order::where('customer_id', $customer->id))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id'           => $order->id,
                    'order_number' => $order->order_number,
                    'date'         => $order->created_at->format('Y-m-d'),
                    'total'        => $order->total,
                ];
            });

        return response()->json([
            'customer_name' => $customer->name,
            'orders'        => $orders,
        ]);
    }

    public function show($orderId)
    {
        $order = Order::with('customer', 'items.product', 'payments', 'refunds', 'user')->findOrFail($orderId);
        return view('admin.orders.show', compact('order'));
    }

    public function productStatement(Request $request)
    {
        $start     = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end       = $request->input('end_date', now()->endOfMonth()->toDateString());
        $productId = $request->input('product_id');

        $products  = $this->scopeBranch(Product::query())->orderBy('name')->get();
        $statement = null;

        if ($productId) {
            $product = Product::findOrFail($productId);

            $items = OrderItem::with(['order.customer'])
                ->where('product_id', $productId)
                ->whereHas('order', function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end])
                      ->where('status', '!=', 'cancelled');
                    $this->scopeBranch($q);
                })
                ->get();

            $totalQty     = $items->sum('quantity');
            $totalRevenue = $items->sum('total_price');
            $totalCost    = $totalQty * ($product->cost_price ?? 0);
            $totalProfit  = $totalRevenue - $totalCost;

            $salesByPrice = $items->groupBy('unit_price')->map(function ($group, $price) {
                return [
                    'price'    => $price,
                    'quantity' => $group->sum('quantity'),
                    'revenue'  => $group->sum('total_price'),
                ];
            })->values();

            $statement = [
                'product'        => $product,
                'items'          => $items,
                'total_qty'      => $totalQty,
                'total_revenue'  => $totalRevenue,
                'total_cost'     => $totalCost,
                'total_profit'   => $totalProfit,
                'sales_by_price' => $salesByPrice,
            ];
        }

        return view('admin.reports.product_statement', compact('products', 'statement', 'start', 'end', 'productId'));
    }
}
