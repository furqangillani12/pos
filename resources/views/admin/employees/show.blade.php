@extends('layouts.admin')

@section('title', 'Employee Details')

@section('content')
    <div class="bg-white p-6 rounded shadow space-y-3 max-w-xl">
        <div><strong>Name:</strong> {{ $employee->user->name }}</div>
        <div><strong>Email:</strong> {{ $employee->user->email }}</div>
        <div><strong>Phone:</strong> {{ $employee->phone }}</div>
        <div><strong>Address:</strong> {{ $employee->address }}</div>
        <div><strong>Salary:</strong> ${{ number_format($employee->salary) }}</div>
        <div><strong>Joining Date:</strong> {{ \Carbon\Carbon::parse($employee->joining_date)->format('d M Y') }}</div>
        <div><strong>Role:</strong> {{ ucfirst($employee->user->roles->pluck('name')->first()) }}</div>
    </div>
@endsection
