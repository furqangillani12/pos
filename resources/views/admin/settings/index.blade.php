@extends('layouts.admin')

@section('content')
    <div class="p-3 sm:p-6">
        <h1 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4 sm:mb-6">POS Settings</h1>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ═══════════════════════════════════════
                 PAYMENT METHODS
            ═══════════════════════════════════════ --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                        </svg>
                        Payment Methods
                    </h2>
                    <span class="text-xs text-gray-400">{{ $paymentMethods->where('is_active', true)->count() }} active</span>
                </div>

                <div class="p-5">
                    {{-- Add New --}}
                    <form action="{{ route('admin.settings.payment-methods.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="text" name="name" placeholder="Value (e.g. jazzcash)" required
                                class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <input type="text" name="label" placeholder="Label (e.g. Jazz)" required
                                class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 whitespace-nowrap">
                                + Add
                            </button>
                        </div>
                    </form>

                    {{-- List --}}
                    <div class="space-y-2">
                        @forelse($paymentMethods as $pm)
                            <div x-data="{ editing: false }" class="flex flex-col sm:flex-row sm:items-center gap-2 p-3 rounded-lg border {{ $pm->is_active ? 'border-gray-200 bg-white' : 'border-gray-100 bg-gray-50 opacity-60' }}">
                                <div class="flex-1 min-w-0">
                                    {{-- Display mode --}}
                                    <div x-show="!editing" class="flex items-center gap-2 flex-wrap">
                                        <span class="font-medium text-gray-800 text-sm">{{ $pm->label }}</span>
                                        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">{{ $pm->name }}</span>
                                    </div>

                                    {{-- Edit mode --}}
                                    <form x-show="editing" x-cloak
                                        action="{{ route('admin.settings.payment-methods.update', $pm) }}" method="POST"
                                        class="flex flex-wrap gap-2">
                                        @csrf @method('PUT')
                                        <input type="text" name="name" value="{{ $pm->name }}"
                                            class="w-full sm:w-28 border border-gray-300 rounded px-2 py-1 text-xs">
                                        <input type="text" name="label" value="{{ $pm->label }}"
                                            class="w-full sm:w-28 border border-gray-300 rounded px-2 py-1 text-xs">
                                        <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-medium">Save</button>
                                        <button type="button" @click="editing = false" class="text-gray-400 hover:text-gray-600 text-xs">Cancel</button>
                                    </form>
                                </div>

                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    {{-- Edit --}}
                                    <button @click="editing = !editing" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        </svg>
                                    </button>

                                    {{-- Toggle Active --}}
                                    <form action="{{ route('admin.settings.payment-methods.toggle', $pm) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="{{ $pm->is_active ? 'Disable' : 'Enable' }}"
                                            class="{{ $pm->is_active ? 'text-green-500 hover:text-green-700' : 'text-gray-400 hover:text-gray-600' }}">
                                            @if($pm->is_active)
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.settings.payment-methods.destroy', $pm) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Delete this payment method?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-4">No payment methods yet. Add one above.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════
                 DISPATCH METHODS
            ═══════════════════════════════════════ --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                        </svg>
                        Dispatch Methods
                    </h2>
                    <span class="text-xs text-gray-400">{{ $dispatchMethods->where('is_active', true)->count() }} active</span>
                </div>

                <div class="p-5">
                    {{-- Add New --}}
                    <form action="{{ route('admin.settings.dispatch-methods.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                            <input type="text" name="name" placeholder="Name (e.g. TCS)" required
                                class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                            <div class="flex items-center gap-2">
                                <label class="flex items-center gap-1.5 text-sm text-gray-600 whitespace-nowrap">
                                    <input type="checkbox" name="has_tracking" value="1" class="rounded border-gray-300 text-orange-600">
                                    Tracking
                                </label>
                                <button type="submit"
                                    class="px-4 py-2 bg-orange-600 text-white rounded text-sm hover:bg-orange-700 whitespace-nowrap">
                                    + Add
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- List --}}
                    <div class="space-y-2">
                        @forelse($dispatchMethods as $dm)
                            <div x-data="{ editing: false }" class="flex flex-col sm:flex-row sm:items-center gap-2 p-3 rounded-lg border {{ $dm->is_active ? 'border-gray-200 bg-white' : 'border-gray-100 bg-gray-50 opacity-60' }}">
                                <div class="flex-1 min-w-0">
                                    {{-- Display mode --}}
                                    <div x-show="!editing" class="flex items-center gap-2 flex-wrap">
                                        <span class="font-medium text-gray-800 text-sm">{{ $dm->name }}</span>
                                        @if($dm->has_tracking)
                                            <span class="text-xs text-orange-600 bg-orange-50 px-2 py-0.5 rounded">Tracking</span>
                                        @endif
                                    </div>

                                    {{-- Edit mode --}}
                                    <form x-show="editing" x-cloak
                                        action="{{ route('admin.settings.dispatch-methods.update', $dm) }}" method="POST"
                                        class="flex flex-wrap gap-2 items-center">
                                        @csrf @method('PUT')
                                        <input type="text" name="name" value="{{ $dm->name }}"
                                            class="w-full sm:w-32 border border-gray-300 rounded px-2 py-1 text-xs">
                                        <label class="flex items-center gap-1 text-xs text-gray-600">
                                            <input type="checkbox" name="has_tracking" value="1" {{ $dm->has_tracking ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-orange-600">
                                            Track
                                        </label>
                                        <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-medium">Save</button>
                                        <button type="button" @click="editing = false" class="text-gray-400 hover:text-gray-600 text-xs">Cancel</button>
                                    </form>
                                </div>

                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    {{-- Edit --}}
                                    <button @click="editing = !editing" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        </svg>
                                    </button>

                                    {{-- Toggle Active --}}
                                    <form action="{{ route('admin.settings.dispatch-methods.toggle', $dm) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="{{ $dm->is_active ? 'Disable' : 'Enable' }}"
                                            class="{{ $dm->is_active ? 'text-green-500 hover:text-green-700' : 'text-gray-400 hover:text-gray-600' }}">
                                            @if($dm->is_active)
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.settings.dispatch-methods.destroy', $dm) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Delete this dispatch method?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-4">No dispatch methods yet. Add one above.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
