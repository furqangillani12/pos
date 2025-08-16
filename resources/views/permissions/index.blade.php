@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto mt-10">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Permissions</h2>
{{--            <a href="{{ route('permissions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Permission</a>--}}
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow rounded">
            <table class="min-w-full table-auto border-collapse">
                <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">Name</th>
{{--                    <th class="p-3">Actions</th>--}}
                </tr>
                </thead>
                <tbody>
                @foreach($permissions as $permission)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">{{ $loop->iteration }}</td>
                        <td class="p-3">{{ $permission->name }}</td>
{{--                        <td class="p-3">--}}
{{--                            <a href="{{ route('permissions.edit', $permission) }}" class="text-blue-600 hover:underline">Edit</a>--}}
{{--                        </td>--}}
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
