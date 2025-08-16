<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        // Default date range = today
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());

        $orders = Order::whereBetween('created_at', [$start, $end])->get();

        $totalSales = $orders->sum('total'); // Assuming 'total' column in orders table
        $totalOrders = $orders->count();

        return view('admin.reports.sales', compact('orders', 'totalSales', 'totalOrders', 'start', 'end'));
    }



    public function topProducts(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());

        $topProducts = OrderItem::selectRaw('product_id, SUM(quantity) as total_quantity, SUM(total_price) as total_revenue')
            ->whereBetween('created_at', [$start, $end])
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
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());

        $orders = Order::with('items.product')
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $totalRevenue = 0;
        $totalCost = 0;

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $product = $item->product;

                // ✅ Fixed field name from $item->price to $item->unit_price
                $revenue = $item->quantity * $item->unit_price;

                // Guard against missing product or cost_price
                $cost = $product ? $item->quantity * ($product->cost_price ?? 0) : 0;

                $totalRevenue += $revenue;
                $totalCost += $cost;
            }
        }

        $profit = $totalRevenue > $totalCost ? $totalRevenue - $totalCost : 0;
        $loss = $totalCost > $totalRevenue ? $totalCost - $totalRevenue : 0;

        return view('admin.reports.profit_loss', compact(
            'start', 'end', 'profit', 'loss', 'totalRevenue', 'totalCost'
        ));
    }


    public function categorySales(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());

        $salesByCategory = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
            )
            ->groupBy('categories.name')
            ->get();


        return view('admin.reports.category_sales', compact('salesByCategory', 'start', 'end'));
    }






    public function customerSales(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());

        $customerSales = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->select(
                'users.name as customer_name',
                'users.email',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total) as total_spent'),
                DB::raw('MAX(orders.created_at) as last_order_date')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->get();

        return view('admin.reports.customer_sales', compact('customerSales', 'start', 'end'));
    }


}
