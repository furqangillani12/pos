<?php

// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $data = [
            'totalSales' => Order::whereDate('created_at', $today)->sum('total'),
            'newOrders' => Order::whereDate('created_at', $today)->count(),
            'lowStockProducts' => Product::where('stock_quantity', '<', 10)->count(),
            'presentEmployees' => Attendance::whereDate('date', $today)
                ->where('status', 'present')
                ->count(),
            'recentOrders' => Order::with('customer')
                ->latest()
                ->take(5)
                ->get(),
            'employeeAttendance' => Attendance::with('employee')
                ->whereDate('date', $today)
                ->get()
        ];

        return view('admin.dashboard', $data);
    }
}
