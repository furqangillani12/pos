@extends('layouts.admin')
<style>
 @media (max-width: 769px)
  {
    .main-div{
        margin-top:50px;
    }
    
  }
</style>

@section('title', 'Add Employee')

@section('content')
    <form action="{{ route('employees.store') }}" method="POST" class="main-div space-y-4">
        @csrf
        @include('admin.employees.partials.form', ['employee' => null])
        <button class="bg-green-600 text-white px-4 py-2 rounded">Create</button>

    </form>
@endsection
