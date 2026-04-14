@extends('layouts.admin')

@section('content')
    <div class="p-6 bg-white rounded shadow">
        <h1 class="text-2xl font-semibold mb-4">Profit / Loss Report</h1>

        <form method="GET" class="mb-6 flex flex-wrap gap-4 items-center">
            <input type="date" name="start_date" value="{{ $start }}" class="border rounded p-2 w-full sm:w-auto" />
            <input type="date" name="end_date" value="{{ $end }}" class="border rounded p-2 w-full sm:w-auto" />
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
        </form>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-green-100 p-4 rounded shadow">
                <h2 class="text-lg font-bold text-green-800">Gross Revenue (کل فروخت)</h2>
                <p class="text-xl font-semibold text-green-900">Rs. {{ number_format($totalRevenue, 0) }}</p>
            </div>
            <div class="bg-orange-100 p-4 rounded shadow">
                <h2 class="text-lg font-bold text-orange-800">Discount (ڈسکاؤنٹ)</h2>
                <p class="text-xl font-semibold text-orange-900">Rs. {{ number_format($totalDiscount ?? 0, 0) }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded shadow border-2 border-green-300">
                <h2 class="text-lg font-bold text-green-800">Net Revenue (خالص آمدنی)</h2>
                <p class="text-xl font-semibold text-green-900">Rs. {{ number_format($netRevenue ?? $totalRevenue, 0) }}</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded shadow">
                <h2 class="text-lg font-bold text-yellow-800">Total Cost (لاگت)</h2>
                <p class="text-xl font-semibold text-yellow-900">Rs. {{ number_format($totalCost, 0) }}</p>
            </div>
            <div class="bg-blue-100 p-4 rounded shadow {{ $profit > 0 ? 'border-2 border-blue-400' : '' }}">
                <h2 class="text-lg font-bold text-blue-800">Profit (منافع)</h2>
                <p class="text-2xl font-bold text-blue-900">Rs. {{ number_format($profit, 0) }}</p>
            </div>
            <div class="bg-red-100 p-4 rounded shadow {{ $loss > 0 ? 'border-2 border-red-400' : '' }}">
                <h2 class="text-lg font-bold text-red-800">Loss (نقصان)</h2>
                <p class="text-2xl font-bold text-red-900">Rs. {{ number_format($loss, 0) }}</p>
            </div>
        </div>

        @if(($totalDelivery ?? 0) > 0 || ($totalTax ?? 0) > 0)
        <div class="mt-4 bg-gray-50 p-4 rounded shadow text-sm text-gray-600">
            <p><strong>Note:</strong> Delivery charges (Rs. {{ number_format($totalDelivery ?? 0, 0) }}) and Tax (Rs. {{ number_format($totalTax ?? 0, 0) }}) are excluded from profit calculation.</p>
        </div>
        @endif
    </div>
@endsection
