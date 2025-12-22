@extends('layouts.admin')

@section('content')
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-semibold mb-4 text-gray-800">Edit Customer</h1>
        <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
            @method('PUT')
            @include('admin.customers._form', ['buttonText' => 'Update Customer'])
        </form>
    </div>
@endsection
