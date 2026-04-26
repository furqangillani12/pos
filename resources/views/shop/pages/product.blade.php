@extends('shop.layouts.app')

@section('title', $product->meta_title ?: $product->name)
@section('description', $product->meta_description ?: ($product->summary ?: \Str::limit(strip_tags($product->description), 150)))

@section('content')
@php
    $price    = shop_product_price($product);
    $original = (float) ($product->price ?? 0);
    $hasSale  = $original > 0 && $original > $price;
    $cover    = shop_image($product->image);
    $gallery  = collect([$product->image])
        ->merge(is_array($product->gallery) ? $product->gallery : [])
        ->filter()->unique()->values();
    $stock    = method_exists($product, 'getStockForBranch') && $product->branch_id
        ? $product->getStockForBranch($product->branch_id)
        : ($product->stock_quantity ?? 0);
    $inWishlist = auth('customer')->check()
        && $product->wishlists()->where('customer_id', auth('customer')->id())->exists();
@endphp

<section class="py-10 sm:py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="text-xs text-gray-500 mb-6 flex items-center gap-2 reveal">
            <a href="{{ route('shop.home') }}" class="hover:text-cyan-700">Home</a> /
            @if ($product->category) <a href="{{ route('shop.category', $product->category->slug ?? '#') }}" class="hover:text-cyan-700">{{ $product->category->name }}</a> / @endif
            <span class="text-gray-700">{{ $product->name }}</span>
        </nav>

        <div class="grid lg:grid-cols-2 gap-10">
            {{-- Gallery --}}
            <div x-data="{ active: 0 }" class="reveal">
                <div class="aspect-square rounded-3xl overflow-hidden bg-gray-100 mb-4 relative">
                    <template x-for="(img, i) in {{ $gallery->map(fn($g) => shop_image($g))->toJson() }}" :key="i">
                        <img :src="img" :alt="'{{ addslashes($product->name) }}'"
                             class="w-full h-full object-cover absolute inset-0 transition-opacity duration-500"
                             :class="active === i ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                    </template>
                </div>
                @if ($gallery->count() > 1)
                    <div class="grid grid-cols-5 gap-2">
                        @foreach ($gallery as $i => $g)
                            <button @click="active = {{ $i }}" type="button"
                                    class="aspect-square rounded-xl overflow-hidden border-2 transition"
                                    :class="active === {{ $i }} ? 'border-cyan-500' : 'border-transparent'">
                                <img src="{{ shop_image($g) }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div x-data="{ qty: 1 }" class="reveal">
                @if ($product->brand)
                    <a href="{{ route('shop.brand', $product->brand->slug) }}" class="text-xs uppercase tracking-widest font-semibold hover:underline" style="color:var(--brand-cyan);">{{ $product->brand->name }}</a>
                @endif
                <h1 class="display text-3xl sm:text-4xl font-bold mt-2 leading-tight">{{ $product->name }}</h1>

                @if ((float) $product->avg_rating > 0)
                    <div class="flex items-center gap-2 mt-3 text-sm">
                        <div class="text-amber-500">
                            @for ($i = 1; $i <= 5; $i++)<i class="fas fa-star {{ $i <= round($product->avg_rating) ? '' : 'text-gray-200' }}"></i>@endfor
                        </div>
                        <span class="text-gray-600">{{ number_format($product->avg_rating, 1) }} · {{ $product->review_count }} reviews</span>
                    </div>
                @endif

                <div class="flex items-baseline gap-3 mt-6">
                    <span class="text-3xl font-extrabold" style="color:var(--brand-navy);">{{ shop_price($price) }}</span>
                    @if ($hasSale)
                        <span class="text-lg text-gray-400 line-through">{{ shop_price($original) }}</span>
                        <span class="chip" style="background:#fee2e2;color:#b91c1c;">SAVE {{ shop_price($original - $price) }}</span>
                    @endif
                </div>

                @if ($product->summary)
                    <p class="text-gray-600 mt-5 leading-relaxed">{{ $product->summary }}</p>
                @endif

                <div class="mt-6 inline-flex items-center gap-2 text-sm">
                    @if ($stock > 0)
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-emerald-700 font-semibold">In stock</span>
                    @else
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <span class="text-rose-700 font-semibold">Out of stock</span>
                    @endif
                </div>

                {{-- Add to bag --}}
                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <div class="inline-flex items-center bg-gray-100 rounded-xl">
                        <button type="button" @click="qty = Math.max(1, qty - 1)" class="px-4 py-3 text-gray-600 hover:text-gray-900"><i class="fas fa-minus text-xs"></i></button>
                        <input type="number" min="1" x-model.number="qty" class="w-14 bg-transparent text-center font-bold border-0 focus:ring-0">
                        <button type="button" @click="qty = qty + 1" class="px-4 py-3 text-gray-600 hover:text-gray-900"><i class="fas fa-plus text-xs"></i></button>
                    </div>
                    <button type="button" @click="addToCart({{ $product->id }}, qty)"
                            class="btn btn-dark flex-1 sm:flex-none" {{ $stock <= 0 ? 'disabled' : '' }}>
                        <i class="fas fa-bag-shopping"></i> Add to bag
                    </button>
                    @auth('customer')
                        <button type="button" onclick="toggleWishlist({{ $product->id }}, this)"
                                class="w-12 h-12 rounded-xl border border-gray-200 hover:border-rose-300 transition flex items-center justify-center {{ $inWishlist ? 'text-rose-500' : 'text-gray-500' }}">
                            <i class="{{ $inWishlist ? 'fas' : 'far' }} fa-heart"></i>
                        </button>
                    @endauth
                </div>

                {{-- Trust strip --}}
                <div class="grid grid-cols-3 gap-3 mt-8 pt-6 border-t border-gray-100 text-xs text-gray-600">
                    <div class="flex items-center gap-2"><i class="fas fa-truck" style="color:var(--brand-cyan);"></i> Fast delivery</div>
                    <div class="flex items-center gap-2"><i class="fas fa-shield-halved" style="color:var(--brand-cyan);"></i> Authentic</div>
                    <div class="flex items-center gap-2"><i class="fas fa-rotate-left" style="color:var(--brand-cyan);"></i> 7-day returns</div>
                </div>

                @if ($product->description)
                    <div class="mt-8 prose max-w-none text-gray-700">
                        <h3 class="display text-xl font-bold mb-3 text-gray-900">About this product</h3>
                        <div>{!! nl2br(e($product->description)) !!}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Reviews --}}
        <div class="mt-16 grid lg:grid-cols-[1fr_320px] gap-10 reveal">
            <div>
                <h2 class="display text-2xl font-bold mb-6">Customer reviews</h2>
                @forelse ($reviews as $r)
                    <div class="border-t border-gray-100 py-5">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="text-amber-500 text-sm">
                                @for ($i = 1; $i <= 5; $i++)<i class="fas fa-star {{ $i <= $r->rating ? '' : 'text-gray-200' }}"></i>@endfor
                            </div>
                            <span class="text-sm font-semibold">{{ $r->customer?->name ?? 'Customer' }}</span>
                            <span class="text-xs text-gray-400">{{ $r->created_at->diffForHumans() }}</span>
                        </div>
                        @if ($r->title)<div class="font-semibold text-gray-800">{{ $r->title }}</div>@endif
                        @if ($r->body)<p class="text-gray-600 mt-1">{{ $r->body }}</p>@endif
                    </div>
                @empty
                    <p class="text-gray-500 italic">No reviews yet — be the first!</p>
                @endforelse
            </div>

            @auth('customer')
                <form method="POST" action="{{ route('shop.review.store', $product->slug) }}"
                      class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-24 self-start"
                      x-data="{ rating: 5 }">
                    @csrf
                    <h3 class="font-bold text-gray-900 mb-3">Write a review</h3>
                    <div class="flex gap-1 text-2xl text-amber-400 mb-4">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" @click="rating = {{ $i }}" :class="rating >= {{ $i }} ? '' : 'text-gray-200'">
                                <i class="fas fa-star"></i>
                            </button>
                        @endfor
                        <input type="hidden" name="rating" :value="rating">
                    </div>
                    <input type="text" name="title" placeholder="Headline (optional)" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm mb-2">
                    <textarea name="body" rows="3" placeholder="Tell others what you think..." class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                    <button type="submit" class="btn btn-dark btn-block mt-3 !text-xs">Submit review</button>
                </form>
            @else
                <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center self-start">
                    <p class="text-sm text-gray-600 mb-3">Want to share your thoughts?</p>
                    <a href="{{ route('shop.login') }}" class="btn btn-dark btn-block !text-xs">Sign in to review</a>
                </div>
            @endauth
        </div>

        {{-- Related --}}
        @if ($related->isNotEmpty())
            <div class="mt-16 reveal">
                <h2 class="display text-2xl sm:text-3xl font-bold mb-6">You might also like</h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach ($related as $product)
                        @include('shop.partials.product-card', compact('product'))
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
