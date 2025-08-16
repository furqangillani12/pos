@extends('layouts.admin')

@section('title', 'Suppliers')

@section('content')
    <div class="mb-4">
        <a href="{{ route('suppliers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow inline-block">
            + Add Supplier
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded bg-green-100 text-green-800 border border-green-300">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm bg-white rounded shadow-md overflow-hidden">
            <thead class="bg-gray-100 text-gray-700 font-semibold">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Company</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Phone</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            @forelse($suppliers as $index => $supplier)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                    <td class="px-4 py-3">{{ $supplier->name }}</td>
                    <td class="px-4 py-3">{{ $supplier->company_name ?? 'N/A' }}</td>
                    <td class="px-4 py-3">{{ $supplier->email ?? 'N/A' }}</td>
                    <td class="px-4 py-3">{{ $supplier->phone }}</td>
                    <td class="px-4 py-3 space-x-2 whitespace-nowrap">
                        <a href="{{ route('suppliers.show', $supplier) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">View</a>
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">Edit</a>
                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete supplier?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">No suppliers found</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($suppliers->hasPages())
            <div class="mt-4">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>
@endsection
