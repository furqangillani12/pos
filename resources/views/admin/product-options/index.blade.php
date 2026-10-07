@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Sizes &amp; Colors</h1>
            <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Back to Products</a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $errors->first() }}</div>
        @endif

        <p class="text-sm text-gray-500 mb-4">These lists fill the Size and Color dropdowns on the Add / Edit Product page.</p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach (['size' => $sizes, 'color' => $colors] as $type => $items)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-3 border-b bg-gray-50 font-semibold text-gray-700">
                        <i class="fas {{ $type === 'size' ? 'fa-ruler-combined' : 'fa-palette' }} mr-1 text-gray-400"></i>
                        {{ $type === 'size' ? 'Sizes' : 'Colors' }} <span class="text-gray-400 font-normal">({{ $items->count() }})</span>
                    </div>
                    <form method="POST" action="{{ route('product-options.store', $type) }}" class="flex gap-2 p-4 border-b">
                        @csrf
                        <input type="text" name="name" required maxlength="100" placeholder="{{ $type === 'size' ? 'e.g. S, M, L, XL, 52, 54' : 'e.g. Black, Navy, Maroon' }}"
                               class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <input type="number" name="sort_order" min="0" placeholder="Order" class="w-20 border border-gray-300 rounded-md px-2 py-2 text-sm">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-md">Add</button>
                    </form>
                    <div class="divide-y divide-gray-100">
                        @forelse ($items as $it)
                            <div class="flex items-center gap-2 px-4 py-2 {{ $it->is_active ? '' : 'opacity-50' }}" x-data="{ edit: false }">
                                <form method="POST" action="{{ route('product-options.update', [$type, $it->id]) }}" class="flex-1 flex items-center gap-2">
                                    @csrf @method('PUT')
                                    <span x-show="!edit" class="font-medium text-gray-800">{{ $it->name }}</span>
                                    <span x-show="!edit" class="text-xs text-gray-400">#{{ $it->sort_order }}</span>
                                    <input x-show="edit" x-cloak type="text" name="name" value="{{ $it->name }}" class="flex-1 border border-gray-300 rounded px-2 py-1 text-sm">
                                    <input x-show="edit" x-cloak type="number" name="sort_order" value="{{ $it->sort_order }}" class="w-16 border border-gray-300 rounded px-2 py-1 text-sm">
                                    <button x-show="edit" x-cloak class="text-xs bg-blue-600 text-white px-2 py-1 rounded">Save</button>
                                </form>
                                <button type="button" @click="edit = !edit" class="text-xs text-gray-500 hover:text-blue-600" title="Edit"><i class="fas fa-pen"></i></button>
                                <form method="POST" action="{{ route('product-options.toggle', [$type, $it->id]) }}">
                                    @csrf @method('PATCH')
                                    <button class="text-xs {{ $it->is_active ? 'text-green-600' : 'text-gray-400' }}" title="{{ $it->is_active ? 'Disable' : 'Enable' }}">
                                        <i class="fas {{ $it->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} text-lg"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('product-options.destroy', [$type, $it->id]) }}" onsubmit="return confirm('Delete {{ addslashes($it->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-400 hover:text-red-600" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        @empty
                            <div class="px-4 py-6 text-center text-sm text-gray-400">None yet — add the first one above.</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
