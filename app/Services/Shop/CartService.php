<?php

namespace App\Services\Shop;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

/**
 * Single source of truth for the storefront cart.
 *
 * - Logged-in customer: cart_items rows scoped by customer_id.
 * - Guest: cart_items rows scoped by session_id (random UUID stored in session).
 *
 * On login we merge the guest cart into the customer's cart automatically.
 */
class CartService
{
    /** Returns Eloquent builder for the active basket (customer or session). */
    public function query()
    {
        if (Auth::guard('customer')->check()) {
            return CartItem::with('product.category', 'product.brand')
                ->where('customer_id', Auth::guard('customer')->id());
        }
        return CartItem::with('product.category', 'product.brand')
            ->where('session_id', $this->sessionKey());
    }

    public function items()
    {
        return $this->query()->latest('id')->get();
    }

    public function count(): int
    {
        return (int) $this->query()->sum('qty');
    }

    public function subtotal(): float
    {
        return (float) $this->items()->sum(fn ($i) => (float) $i->qty * (float) $i->unit_price);
    }

    public function add(Product $product, float $qty = 1, ?string $size = null, ?string $color = null): CartItem
    {
        $price = shop_product_price($product);

        $existing = $this->query()
            ->where('product_id', $product->id)
            ->where('selected_size', $size)
            ->where('selected_color', $color)
            ->first();

        if ($existing) {
            $existing->qty = (float) $existing->qty + $qty;
            $existing->unit_price = $price;
            $existing->save();
            return $existing;
        }

        return CartItem::create([
            'customer_id'    => Auth::guard('customer')->id(),
            'session_id'     => Auth::guard('customer')->check() ? null : $this->sessionKey(),
            'product_id'     => $product->id,
            'branch_id'      => $product->branch_id,
            'qty'            => $qty,
            'unit_price'     => $price,
            'selected_size'  => $size,
            'selected_color' => $color,
        ]);
    }

    public function update(CartItem $item, float $qty): CartItem
    {
        if (!$this->ownsItem($item)) abort(403);
        $item->qty = max(0.01, $qty);
        $item->save();
        return $item;
    }

    public function remove(CartItem $item): void
    {
        if (!$this->ownsItem($item)) abort(403);
        $item->delete();
    }

    public function clear(): void
    {
        $this->query()->delete();
        Session::forget('shop.coupon');
    }

    public function ownsItem(CartItem $item): bool
    {
        if (Auth::guard('customer')->check()) {
            return (int) $item->customer_id === (int) Auth::guard('customer')->id();
        }
        return $item->session_id === $this->sessionKey();
    }

    /**
     * Merge any guest cart rows into the customer's cart on login.
     * Same-product+size+color rows are summed.
     */
    public function mergeGuestIntoCustomer(int $customerId): void
    {
        $sessionKey = $this->sessionKey();
        $guest = CartItem::where('session_id', $sessionKey)->get();

        foreach ($guest as $row) {
            $existing = CartItem::where('customer_id', $customerId)
                ->where('product_id', $row->product_id)
                ->where('selected_size', $row->selected_size)
                ->where('selected_color', $row->selected_color)
                ->first();

            if ($existing) {
                $existing->qty = (float) $existing->qty + (float) $row->qty;
                $existing->save();
                $row->delete();
            } else {
                $row->customer_id = $customerId;
                $row->session_id = null;
                $row->save();
            }
        }
    }

    /** ── Coupon (stored in session as code; resolved live each cart render) ─ */
    public function applyCoupon(string $code): ?Coupon
    {
        $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper($code)])->first();
        if (!$coupon || !$coupon->isUsable($this->subtotal())) return null;
        Session::put('shop.coupon', $coupon->code);
        return $coupon;
    }

    public function removeCoupon(): void
    {
        Session::forget('shop.coupon');
    }

    public function activeCoupon(): ?Coupon
    {
        $code = Session::get('shop.coupon');
        if (!$code) return null;
        $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper($code)])->first();
        if (!$coupon || !$coupon->isUsable($this->subtotal())) {
            Session::forget('shop.coupon');
            return null;
        }
        return $coupon;
    }

    public function discount(): float
    {
        $coupon = $this->activeCoupon();
        return $coupon ? $coupon->discountFor($this->subtotal()) : 0.0;
    }

    public function totals(): array
    {
        $sub = $this->subtotal();
        $disc = $this->discount();
        return [
            'subtotal' => round($sub, 2),
            'discount' => round($disc, 2),
            'total'    => round(max(0, $sub - $disc), 2),
            'count'    => $this->count(),
        ];
    }

    /** Stable session key for guest carts. */
    public function sessionKey(): string
    {
        $key = Session::get('shop.session_key');
        if (!$key) {
            $key = (string) Str::uuid();
            Session::put('shop.session_key', $key);
        }
        return $key;
    }
}
