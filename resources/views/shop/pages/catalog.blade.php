@extends('shop.layouts.app')

@section('title', $category?->name ?? $brand?->name ?? ($q ? 'Search: ' . $q : 'Shop'))

@section('content')
<section class="hero py-14 sm:py-16 text-center">
    <div class="hero-pattern absolute inset-0"></div>
    <div class="relative max-w-3xl mx-auto px-4 reveal">
        <span class="chip mb-4 inline-block" style="background:rgba(251,191,36,.15);color:#fde68a;border:1px solid rgba(251,191,36,.3);">
            {{ $category ? 'Category' : ($brand ? 'Brand' : ($q ? 'Search' : 'Shop')) }}
        </span>
        <h1 class="display text-4xl sm:text-5xl font-bold text-white">
            {{ $category?->name ?? $brand?->name ?? ($q ? 'Results for "' . $q . '"' : 'All products') }}
        </h1>
        <p class="text-sky-100/80 mt-3">{{ $products->total() }} products found</p>
    </div>
</section>

<section class="py-10 sm:py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-[260px_1fr] gap-8">

        {{-- Filters sidebar --}}
        <aside class="lg:sticky lg:top-24 lg:self-start space-y-6 reveal">
            <form method="GET" class="space-y-6">
                @if ($q) <input type="hidden" name="q" value="{{ $q }}"> @endif

                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-800 mb-3">Sort by</h3>
                    <select name="sort" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        <option value="newest"    @selected($sort==='newest')>Newest first</option>
                        <option value="price_asc" @selected($sort==='price_asc')>Price: low to high</option>
                        <option value="price_desc"@selected($sort==='price_desc')>Price: high to low</option>
                        <option value="rating"    @selected($sort==='rating')>Top rated</option>
                        <option value="name"      @selected($sort==='name')>Name A-Z</option>
                    </select>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-800 mb-3">Price (Rs.)</h3>
                    <div class="flex gap-2">
                        <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="Min" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="Max" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <button type="submit" class="btn btn-dark btn-block mt-3 !py-2 !text-xs">Apply</button>
                </div>

                @if ($allBrands->isNotEmpty() && !$brand)
                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <h3 class="font-bold text-gray-800 mb-3">Brand</h3>
                        <div class="space-y-2 max-h-56 overflow-y-auto">
                            @foreach ($allBrands as $b)
                                <label class="flex items-center gap-2 text-sm cursor-pointer hover:text-cyan-700">
                                    <input type="radio" name="brand_id" value="{{ $b->id }}" onchange="this.form.submit()"
                                           {{ request('brand_id') == $b->id ? 'checked' : '' }}>
                                    {{ $b->name }}
                                </label>
                            @endforeach
                            @if (request('brand_id'))
                                <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except('brand_id', 'page'))) }}" class="text-xs text-rose-500 hover:underline">Clear brand</a>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($allCategories->isNotEmpty() && !$category)
                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <h3 class="font-bold text-gray-800 mb-3">Categories</h3>
                        <ul class="space-y-1 text-sm">
                            @foreach ($allCategories as $cat)
                                <li><a href="{{ route('shop.category', $cat->slug) }}" class="block py-1 hover:text-cyan-700 transition">{{ $cat->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </form>
        </aside>

        {{-- Products grid --}}
        <div>
            @if ($products->count())
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 sm:gap-5 reveal-stagger">
                    @foreach ($products as $product)
                        @include('shop.partials.product-card', compact('product'))
                    @endforeach
                </div>
                <div class="mt-10">{{ $products->onEachSide(1)->links() }}</div>
            @else
                <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center reveal">
                    <i class="fas fa-magnifying-glass text-4xl text-gray-300 mb-3"></i>
                    <p class="font-bold text-gray-700">No products found</p>
                    <p class="text-sm text-gray-500 mt-1">Try a different filter or browse our full catalog.</p>
                    <a href="{{ route('shop.catalog') }}" class="btn btn-dark mt-4 !text-xs">Browse all</a>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>.hero-pattern{background-image:linear-gradient(rgba(255,255,255,.05) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.05) 1px,transparent 1px);background-size:48px 48px;mask-image:radial-gradient(ellipse at center,black 30%,transparent 80%);-webkit-mask-image:radial-gradient(ellipse at center,black 30%,transparent 80%);}</style>
@endpush
