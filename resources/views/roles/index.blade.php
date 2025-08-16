@extends('layouts.admin')

@section('content')
    <div class="max-w-5xl mx-auto mt-10">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Roles</h2>
            <a href="{{ route('roles.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Role</a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow rounded overflow-x-auto">
            <table class="min-w-full table-auto border-collapse">
                <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">Role</th>
                    <th class="p-3">Permissions</th>
                    <th class="p-3">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">{{ $loop->iteration }}</td>
                        <td class="p-3">{{ $role->name }}</td>
                        <td class="p-3">
                            @foreach($role->permissions as $permission)
                                <span class="inline-block bg-gray-200 text-sm rounded px-2 py-1 mr-1">{{ $permission->name }}</span>
                            @endforeach
                        </td>
                        <td class="p-3 flex space-x-2">
                            <a href="{{ route('roles.edit', $role) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
