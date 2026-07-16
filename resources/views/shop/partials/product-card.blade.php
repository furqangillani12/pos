@php
    /** @var \App\Models\Product $product */
    $price     = shop_product_price($product);
    $strike    = shop_strike_price($product);
    $hasSale   = $strike !== null;
    // Discount % off the strike (retail) price — resellers see this instead of a
    // plain "SALE" so they can read their margin at a glance (client feedback #19).
    $pctOff    = ($hasSale && $strike > 0) ? (int) round(($strike - $price) / $strike * 100) : 0;
    $cover     = shop_image($product->image);
    // Out of stock only when the product tracks inventory and has none (client #17).
    $outOfStock = ($product->track_inventory ?? false) && (($product->stock_quantity ?? 0) <= 0);
    $badge     = $hasSale ? 'sale' : ($product->condition_label ?? 'default');
    $inWishlist = auth('customer')->check()
        && $product->wishlists()->where('customer_id', auth('customer')->id())->exists();
@endphp

<article class="product-card group">
    <a href="{{ route('shop.product', $product->slug ?? $product->id) }}" class="img-wrap block">
        <img src="{{ $cover }}" alt="{{ $product->name }}" loading="lazy">

        @if ($badge !== 'default')
            <span class="chip absolute top-3 left-3 z-10"
                  style="background:{{ $badge === 'sale' ? '#fee2e2' : ($badge === 'new' ? '#e8f1fb' : '#cfeefb') }};
                         color:{{ $badge === 'sale' ? '#b91c1c' : ($badge === 'new' ? '#0e7490' : '#92400e') }};">
                {{ $badge === 'sale' ? (($pctOff > 0) ? $pctOff.'% OFF' : 'SALE') : ($badge === 'new' ? 'NEW' : 'HOT') }}
            </span>
        @endif

        @if ($outOfStock)
            <span class="chip absolute top-3 right-3 z-10" style="background:#111827;color:#fff;">Out of stock</span>
        @endif

        <div class="quick">
            @auth('customer')
                <button type="button" onclick="event.preventDefault(); toggleWishlist({{ $product->id }}, this)"
                        class="{{ $inWishlist ? 'text-blue-500' : '' }}" title="Wishlist">
                    <i class="{{ $inWishlist ? 'fas' : 'far' }} fa-heart"></i>
                </button>
            @else
                <button type="button" onclick="event.preventDefault(); window.location='{{ route('shop.login') }}'" title="Wishlist">
                    <i class="far fa-heart"></i>
                </button>
            @endauth
            @unless ($outOfStock)
                <button type="button" onclick="event.preventDefault(); addToCart({{ $product->id }})" title="Quick add">
                    <i class="fas fa-plus"></i>
                </button>
            @endunless
        </div>
    </a>

    <div class="p-4">
        @if ($product->brand)
            <div class="text-[10px] uppercase tracking-widest text-gray-400 font-semibold mb-1">{{ $product->brand->name }}</div>
        @endif
        <a href="{{ route('shop.product', $product->slug ?? $product->id) }}"
           class="font-semibold text-gray-900 leading-snug line-clamp-2 hover:text-blue-700 transition">
            {{ $product->name }}
        </a>

        @if ($product->barcode)
            <div class="text-[10px] text-gray-400 font-mono mt-1">Code: {{ $product->barcode }}</div>
        @endif

        <div class="flex items-center flex-wrap gap-x-2 gap-y-0.5 mt-2">
            <span class="font-bold text-base" style="color:var(--brand-navy);">{{ shop_price($price) }}</span>
            @if ($hasSale)
                <span class="text-xs text-gray-400 line-through">{{ shop_price($strike) }}</span>
                @if ($pctOff > 0)
                    <span class="text-[10px] font-bold text-emerald-600">{{ $pctOff }}% off</span>
                @endif
                @if (shop_is_reseller())
                    <span class="text-[10px] font-semibold text-emerald-600">· retail save {{ shop_price($strike - $price) }}</span>
                @endif
            @endif
        </div>

        @if ((float) $product->avg_rating > 0)
            <div class="flex items-center gap-1 mt-1.5 text-xs text-amber-500">
                @for ($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $i <= round($product->avg_rating) ? '' : 'text-gray-200' }}"></i>
                @endfor
                <span class="text-gray-400 ml-1">({{ $product->review_count }})</span>
            </div>
        @endif

        {{-- Always-visible CTA (client #12) — or a restock request when sold out (#17) --}}
        <div class="mt-3">
            @if ($outOfStock)
                <button type="button" onclick="requestItem({{ $product->id }}, @js($product->name))"
                        class="w-full text-sm font-semibold rounded-lg py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                    <i class="far fa-bell"></i> Request this item
                </button>
            @else
                <button type="button" onclick="addToCart({{ $product->id }})"
                        class="w-full text-sm font-semibold rounded-lg py-2 text-white transition hover:opacity-90" style="background:var(--brand-navy);">
                    <i class="fas fa-cart-plus"></i> Add to Cart
                </button>
            @endif
        </div>
    </div>
</article>
