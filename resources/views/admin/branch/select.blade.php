@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-center h-screen bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6">Select Branch</h2>
            <form action="{{ route('branch.store') }}" method="POST">
                @csrf
                <select name="branch_id" class="w-full p-3 border rounded mb-4" required>
                    <option value="">-- Select Branch --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded hover:bg-blue-700">
                    Continue
                </button>

            </form>
        </div>
    </div>
@endsection
