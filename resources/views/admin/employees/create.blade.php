@extends('layouts.admin')

@section('title', 'Add Employee')

@section('content')
    <form action="{{ route('employees.store') }}" method="POST" class="space-y-4">
        @csrf
        @include('admin.employees.partials.form', ['employee' => null])
        <button class="bg-green-600 text-white px-4 py-2 rounded">Create</button>

    </form>
@endsection
