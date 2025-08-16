@extends('layouts.admin')

@section('content')
    <div class="p-6 bg-white rounded shadow">
        <h1 class="text-2xl font-semibold mb-4">Profit / Loss Report</h1>

        <form method="GET" class="mb-6 flex flex-wrap gap-4">
            <input type="date" name="start_date" value="{{ $start }}" class="border rounded p-2" />
            <input type="date" name="end_date" value="{{ $end }}" class="border rounded p-2" />
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
        </form>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-green-100 p-4 rounded shadow">
                <h2 class="text-lg font-bold text-green-800">Total Revenue</h2>
                <p class="text-xl font-semibold text-green-900">₨{{ number_format($totalRevenue, 2) }}</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded shadow">
                <h2 class="text-lg font-bold text-yellow-800">Total Cost</h2>
                <p class="text-xl font-semibold text-yellow-900">₨{{ number_format($totalCost, 2) }}</p>
            </div>
            <div class="bg-blue-100 p-4 rounded shadow">
                <h2 class="text-lg font-bold text-blue-800">Profit</h2>
                <p class="text-xl font-semibold text-blue-900">₨{{ number_format($profit, 2) }}</p>
            </div>
            <div class="bg-red-100 p-4 rounded shadow">
                <h2 class="text-lg font-bold text-red-800">Loss</h2>
                <p class="text-xl font-semibold text-red-900">₨{{ number_format($loss, 2) }}</p>
            </div>
        </div>
    </div>
@endsection
