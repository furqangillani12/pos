@extends('layouts.admin')

@section('title', 'Edit Employee')

@section('content')
    <form action="{{ route('employees.update', $employee) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        @include('admin.employees.partials.form')
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    </form>
@endsection
