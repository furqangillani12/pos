<?php

use App\Models\CartItem;
use App\Models\Setting;
use App\Services\Shop\CartService;
use Illuminate\Support\Facades\Auth;

if (!function_exists('setting')) {
    /**
     * Read a site setting (social links, contact info, etc.) managed from the
     * admin Settings screen. Cached. Falls back to $default when unset/empty.
     */
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('courier_track_url')) {
    /**
     * Build a public courier tracking URL from a dispatch-method name and a
     * tracking/consignment number. Returns null when we can't form one, so the
     * caller can fall back to plain text.
     */
    function courier_track_url(?string $dispatchMethod, ?string $trackingId): ?string
    {
        $trackingId = trim((string) $trackingId);
        if ($trackingId === '') return null;

        $key = strtolower((string) $dispatchMethod);
        $code = rawurlencode($trackingId);

        return match (true) {
            str_contains($key, 'tcs')      => "https://www.tcsexpress.com/track/?trackingNo={$code}",
            str_contains($key, 'leopard')  => "https://www.leopardscourier.com/leopards-tracking?cn_number={$code}",
            str_contains($key, 'm&p') ,
            str_contains($key, 'mp ')      => "https://mulphilog.com/track-and-trace?tracking={$code}",
            str_contains($key, 'post')     => "https://ep.gov.pk/track.asp?id={$code}",
            str_contains($key, 'daewoo')   => "https://daewoo.com.pk/track-parcel/?tracking={$code}",
            str_contains($key, 'trax')     => "https://sonic.pk/tracking?id={$code}",
            default                        => "https://www.google.com/search?q=" . rawurlencode(trim($dispatchMethod . ' tracking ' . $trackingId)),
        };
    }
}

if (!function_exists('shop_cart_count')) {
    /**
     * Sum of cart item quantities for the current customer or session.
     */
    function shop_cart_count(): int
    {
        return (int) app(CartService::class)->count();
    }
}

if (!function_exists('shop_cart_subtotal')) {
    function shop_cart_subtotal(): float
    {
        return (float) app(CartService::class)->subtotal();
    }
}

if (!function_exists('shop_image')) {
    /**
     * Resolve a stored image path to a public URL, with a graceful placeholder.
     */
    function shop_image(?string $path, string $placeholder = 'https://placehold.co/600x750/f5f1e8/0c1f3d?text=Almufeed'): string
    {
        if (empty($path)) return $placeholder;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
        if (str_starts_with($path, '/')) return $path;
        return asset('storage/' . ltrim($path, '/'));
    }
}

if (!function_exists('shop_price')) {
    /**
     * Format a price the Pakistani way.
     */
    function shop_price($amount): string
    {
        return 'Rs. ' . number_format((float) $amount, 0);
    }
}

if (!function_exists('shop_product_price')) {
    /**
     * Choose the right price for the visitor based on customer_type
     * (retail / reseller / wholesale). Falls back to sale_price.
     */
    function shop_product_price($product): float
    {
        $type = Auth::guard('customer')->user()?->customer_type ?? 'customer';
        return match ($type) {
            'reseller'  => (float) ($product->resale_price    ?: $product->sale_price ?: $product->price),
            'wholesale' => (float) ($product->wholesale_price ?: $product->sale_price ?: $product->price),
            default     => (float) ($product->sale_price ?: $product->price),
        };
    }
}
