@extends('layouts.admin')

@section('content')
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-semibold mb-4 text-gray-800">Customer Details</h1>

        <div class="space-y-3">
            <p><strong>Name:</strong> {{ $customer->name }}</p>
            <p><strong>Email:</strong> {{ $customer->email ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $customer->phone ?? 'N/A' }}</p>
            <p><strong>Address:</strong> {{ $customer->address ?? 'N/A' }}</p>
            <p><strong>Loyalty Points:</strong> {{ $customer->loyalty_points }}</p>
            <p><strong>Type:</strong> {{ $customer->type_label }}</p>
        </div>

        <div class="mt-6 flex gap-3">
            <a href="{{ route('admin.customers.edit', $customer) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</a>
            <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Back</a>
        </div>
    </div>
@endsection
