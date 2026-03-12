@extends('layouts.admin')

@section('title', 'Select Branch')

@section('content')
    <div class="min-h-[70vh] flex items-center justify-center">
        <div class="w-full max-w-2xl">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-store text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Select Branch</h2>
                <p class="text-sm text-gray-500 mt-1">Choose which branch you want to work with</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($branches as $branch)
                    <form method="POST" action="{{ route('admin.branch.store-selection') }}">
                        @csrf
                        <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                        <button type="submit"
                                class="w-full bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-left hover:border-blue-300 hover:shadow-md transition group">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold flex-shrink-0 group-hover:bg-blue-100 transition">
                                    {{ strtoupper(substr($branch->code ?? $branch->name, 0, 2)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-800 group-hover:text-blue-600 transition">{{ $branch->name }}</h3>
                                    @if($branch->address)
                                        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $branch->address }}</p>
                                    @endif
                                    <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                                        <span><i class="fas fa-shopping-cart mr-1"></i>{{ $branch->orders_count ?? 0 }} orders</span>
                                        <span><i class="fas fa-users mr-1"></i>{{ $branch->employees_count ?? 0 }} staff</span>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-300 group-hover:text-blue-400 mt-3"></i>
                            </div>
                        </button>
                    </form>
                @endforeach

                @can('view all branches')
                    <form method="POST" action="{{ route('admin.branch.store-selection') }}">
                        @csrf
                        <input type="hidden" name="branch_id" value="all">
                        <button type="submit"
                                class="w-full bg-gray-50 rounded-xl shadow-sm border border-dashed border-gray-300 p-5 text-left hover:border-blue-300 hover:bg-blue-50/30 transition group">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center text-lg group-hover:bg-blue-100 group-hover:text-blue-600 transition">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-700 group-hover:text-blue-600 transition">All Branches</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">View combined data from all branches</p>
                                </div>
                                <i class="fas fa-chevron-right text-gray-300 group-hover:text-blue-400 mt-3"></i>
                            </div>
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
@endsection
