@extends('layouts.admin')

@section('content')
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @role('admin')
    <div class="space-y-6">

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-blue-600 text-white p-6 rounded-lg shadow">
                <h5 class="text-sm uppercase font-semibold">Today's Sales</h5>
                <h2 class="text-2xl font-bold mt-2">Rs. {{ number_format($totalSales, 2) }}</h2>
            </div>

            <div class="bg-green-600 text-white p-6 rounded-lg shadow">
                <h5 class="text-sm uppercase font-semibold">New Orders</h5>
                <h2 class="text-2xl font-bold mt-2">{{ $newOrders }}</h2>
            </div>

            <div class="bg-yellow-400 text-white p-6 rounded-lg shadow">
                <h5 class="text-sm uppercase font-semibold">Low Stock Items</h5>
                <h2 class="text-2xl font-bold mt-2">{{ $lowStockProducts }}</h2>
            </div>

            <div class="bg-cyan-600 text-white p-6 rounded-lg shadow">
                <h5 class="text-sm uppercase font-semibold">Present Employees</h5>
                <h2 class="text-2xl font-bold mt-2">{{ $presentEmployees }}</h2>
            </div>
        </div>

        <!-- Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Recent Orders Table -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                <h5 class="text-lg font-semibold mb-4">Recent Orders</h5>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left border">
                        <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-3 border">Order #</th>
                            <th class="p-3 border">Customer</th>
                            <th class="p-3 border">Amount</th>
                            <th class="p-3 border">Date</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        @foreach($recentOrders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 border">{{ $order->order_number }}</td>
                                <td class="p-3 border">{{ $order->customer->name ?? 'Walk-in' }}</td>
                                <td class="p-3 border">Rs. {{ number_format($order->total, 2) }}</td>
                                <td class="p-3 border">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Attendance List -->
            <div class="bg-white rounded-lg shadow p-6">
                <h5 class="text-lg font-semibold mb-4">Today's Attendance</h5>
                <ul class="divide-y">
                    @foreach($employeeAttendance as $attendance)
                        <li class="flex justify-between py-2">
                            <span>{{ $attendance->employee->user->name }}</span>
                            <span class="text-sm font-medium rounded-full px-3 py-1
                                {{ $attendance->status == 'present' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($attendance->status) }}
                                @if($attendance->check_in)
                                    ({{ $attendance->check_in }}@if($attendance->check_out)-{{ $attendance->check_out }}@endif)
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>
    @endrole
@endsection
