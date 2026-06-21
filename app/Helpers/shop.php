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

if (!function_exists('wa_link')) {
    /**
     * Build a WhatsApp click-to-chat link (wa.me) for a number + prefilled text.
     * Returns null if no usable number.
     */
    function wa_link(?string $number, string $text = ''): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $number);
        if ($digits === '') return null;
        // Pakistani local 03xx… → 92 3xx…
        if (str_starts_with($digits, '0')) {
            $digits = '92' . ltrim($digits, '0');
        }
        $url = 'https://wa.me/' . $digits;
        if ($text !== '') $url .= '?text=' . rawurlencode($text);
        return $url;
    }
}

if (!function_exists('shop_whatsapp_number')) {
    /** The shop's public WhatsApp number from settings. */
    function shop_whatsapp_number(): ?string
    {
        return setting('social_whatsapp') ?: setting('site_whatsapp') ?: null;
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

if (!function_exists('shop_is_reseller')) {
    /** True when the logged-in customer buys at a reseller/wholesale tier. */
    function shop_is_reseller(): bool
    {
        $type = Auth::guard('customer')->user()?->customer_type ?? 'customer';
        return in_array($type, ['reseller', 'wholesale'], true);
    }
}

if (!function_exists('shop_strike_price')) {
    /**
     * The reference price to show struck-through next to what the visitor pays.
     * - Reseller / wholesale: the RETAIL price (sale_price) so they see their margin.
     * - Retail customer: the list/MRP price (price) when it's higher (a real sale).
     * Returns null when there's nothing meaningful to strike out.
     */
    function shop_strike_price($product): ?float
    {
        $paid = shop_product_price($product);
        if (shop_is_reseller()) {
            $retail = (float) ($product->sale_price ?: $product->price ?: 0);
            return $retail > $paid ? $retail : null;
        }
        $list = (float) ($product->price ?? 0);
        return $list > $paid ? $list : null;
    }
}

if (!function_exists('shop_package_price')) {
    /** The package price for the current visitor's tier (retail/reseller/wholesale). */
    function shop_package_price($package): float
    {
        $type = Auth::guard('customer')->user()?->customer_type ?? 'customer';
        return match ($type) {
            'reseller'  => (float) ($package->resale_price    ?: $package->sale_price),
            'wholesale' => (float) ($package->wholesale_price ?: $package->sale_price),
            default     => (float) $package->sale_price,
        };
    }
}

if (!function_exists('shop_tax_rate')) {
    /** Storefront tax rate (percent value or fixed amount) from settings. */
    function shop_tax_rate(): float
    {
        return (float) setting('shop_tax_rate', 0);
    }
}

if (!function_exists('shop_tax_type')) {
    /** 'percent' (default) or 'fixed' — how the storefront tax is applied. */
    function shop_tax_type(): string
    {
        return setting('shop_tax_type', 'percent') === 'fixed' ? 'fixed' : 'percent';
    }
}

if (!function_exists('shop_tax_amount')) {
    /**
     * Tax on a taxable base, computed exactly like the POS receipt
     * (exclusive: added on top). Returns 0 when no rate is set.
     */
    function shop_tax_amount(float $base): float
    {
        $rate = shop_tax_rate();
        if ($rate <= 0 || $base <= 0) return 0.0;
        return shop_tax_type() === 'fixed' ? $rate : round($base * $rate / 100, 2);
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
