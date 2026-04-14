@extends('layouts.admin')
<style>
 @media (max-width: 769px)
  {
    .main-div{
        margin-top:50px;
    }
    
  }
</style>

@section('title', 'Suppliers')

@section('content')
    <div class="main-div mb-4 flex flex-wrap justify-between items-center gap-3">
        <a href="{{ route('suppliers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow inline-block">
            + Add Supplier
        </a>
        <form method="GET" action="{{ route('suppliers.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, phone, company..."
                class="border border-gray-300 rounded-md px-3 py-2 text-sm w-64">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Search</button>
            @if(request('search'))
                <a href="{{ route('suppliers.index') }}" class="border border-gray-300 text-gray-600 px-4 py-2 rounded text-sm hover:bg-gray-50">Clear</a>
            @endif
        </form>
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
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('suppliers.ledger', $supplier) }}" class="inline-flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white px-2.5 py-1.5 rounded text-xs font-medium" title="Ledger"><i class="fas fa-book"></i> Ledger</a>
                            <a href="{{ route('suppliers.show', $supplier) }}" class="inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white px-2.5 py-1.5 rounded text-xs font-medium"><i class="fas fa-eye"></i> View</a>
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex items-center gap-1 bg-yellow-500 hover:bg-yellow-600 text-white px-2.5 py-1.5 rounded text-xs font-medium"><i class="fas fa-edit"></i> Edit</a>
                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Delete supplier?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white px-2.5 py-1.5 rounded text-xs font-medium"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                        </div>
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
                {{ $suppliers->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection
