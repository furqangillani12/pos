@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-3xl">
    <h1 class="text-2xl font-semibold text-gray-800 mb-1">Edit Coupon</h1>
    <p class="text-sm text-gray-600 mb-6">{{ $coupon->code }}</p>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
            @method('PUT')
            @include('admin.coupons._form')
        </form>
    </div>
</div>
@endsection
