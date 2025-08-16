@extends('layouts.admin')

@section('title', 'Supplier Details')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded shadow p-6">
            <div class="flex justify-between items-start mb-6">
                <h2 class="text-2xl font-semibold">Supplier Details</h2>
                <div class="space-x-2">
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">Edit</a>
                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete supplier?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Delete</button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Basic Information</h3>
                    <div class="space-y-2">
                        <p><span class="font-medium">Name:</span> {{ $supplier->name }}</p>
                        <p><span class="font-medium">Company:</span> {{ $supplier->company_name ?? 'N/A' }}</p>
                        <p><span class="font-medium">Email:</span> {{ $supplier->email ?? 'N/A' }}</p>
                        <p><span class="font-medium">Phone:</span> {{ $supplier->phone }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Address</h3>
                    <p class="whitespace-pre-line">{{ $supplier->address ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-700 mb-4">Purchases from this supplier</h3>
                @if($supplier->purchases->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm bg-gray-50 rounded">
                            <thead class="bg-gray-100 text-gray-700 font-semibold">
                            <tr>
                                <th class="px-4 py-2 text-left">#</th>
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Total</th>
                                <th class="px-4 py-2 text-left">Status</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            @foreach($supplier->purchases as $index => $purchase)
                                <tr class="hover:bg-gray-100">
                                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</td>
                                    <td class="px-4 py-2">{{ number_format($purchase->total_amount, 2) }}</td>
                                    <td class="px-4 py-2">{{ $purchase->status }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">No purchases recorded for this supplier.</p>
                @endif
            </div>

            <div class="mt-6">
                <a href="{{ route('suppliers.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">&larr; Back to Suppliers</a>
            </div>
        </div>
    </div>
@endsection
