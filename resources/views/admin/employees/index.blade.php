@extends('layouts.admin')

@section('title', 'Employees')

@section('content')
    <div class="mb-4">
        <a href="{{ route('employees.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow inline-block">
            + Add Employee
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
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Phone</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            @foreach($employees as $index => $employee)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                    <td class="px-4 py-3">{{ $employee->user->name }}</td>
                    <td class="px-4 py-3">{{ $employee->user->email }}</td>
                    <td class="px-4 py-3">{{ $employee->phone }}</td>
                    <td class="px-4 py-3 space-x-2">
                        <a href="{{ route('employees.show', $employee) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">View</a>
                        <a href="{{ route('employees.edit', $employee) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">Edit</a>
                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete employee?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $employees->links() }}
        </div>
    </div>
@endsection
