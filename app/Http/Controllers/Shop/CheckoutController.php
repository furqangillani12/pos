<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\DispatchMethod;
use App\Models\DeliveryChargeSlab;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Shop\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index()
    {
        $items = $this->cart->items();
        if ($items->isEmpty()) return redirect()->route('shop.cart')->with('shop_error', 'Your bag is empty.');

        $totals = $this->cart->totals();
        $coupon = $this->cart->activeCoupon();
        $customer = Auth::guard('customer')->user();
        $isGuest = !$customer;
        $dispatchMethods = DispatchMethod::active()->get();
        $weight = $items->sum(fn ($i) => (float) ($i->product?->weight ?? 0) * (float) $i->qty);

        return view('shop.pages.checkout', compact('items', 'totals', 'coupon', 'customer', 'isGuest', 'dispatchMethods', 'weight'));
    }

    public function place(Request $request)
    {
        $isGuest = !Auth::guard('customer')->check();

        $rules = [
            'shipping_first_name'  => 'required|string|max:191',
            'shipping_last_name'   => 'nullable|string|max:191',
            'shipping_phone'       => 'required|string|max:30',
            'shipping_address1'    => 'required|string|max:500',
            'shipping_address2'    => 'nullable|string|max:500',
            'shipping_city'        => 'required|string|max:100',
            'shipping_post_code'   => 'nullable|string|max:20',
            'dispatch_method'      => 'required|string|max:100',
            'payment_method'       => 'required|in:cod,bank_transfer',
            'order_notes_customer' => 'nullable|string|max:1000',
        ];
        if ($isGuest) {
            $rules['guest_email'] = 'required|email|max:191';
        }

        $data = $request->validate($rules);

        $items = $this->cart->items();
        if ($items->isEmpty()) return redirect()->route('shop.cart')->with('shop_error', 'Your bag is empty.');

        $customer = Auth::guard('customer')->user();
        $totals   = $this->cart->totals();
        $coupon   = $this->cart->activeCoupon();
        $weight   = $items->sum(fn ($i) => (float) ($i->product?->weight ?? 0) * (float) $i->qty);
        $delivery = $this->resolveDelivery($data['dispatch_method'], $weight);
        $grandTotal = max(0, $totals['total'] + $delivery);

        $order = DB::transaction(function () use ($items, $customer, $isGuest, $data, $totals, $coupon, $delivery, $grandTotal, $weight) {

            // Pick a fulfilment branch — first item's branch_id (or any branch)
            $branchId = $items->first()->branch_id ?? \App\Models\Branch::query()->value('id');

            $order = Order::create([
                'order_number'     => Order::generateOrderNumber($branchId),
                'order_source'     => 'online',
                'order_type'       => 'online',
                'customer_id'      => $customer?->id,
                'customer_email'   => $customer?->email ?? ($data['guest_email'] ?? null),
                'customer_type'    => $customer?->customer_type ?? 'customer',
                'user_id'          => null,
                'branch_id'        => $branchId,
                'subtotal'         => $totals['subtotal'],
                'discount'         => $totals['discount'],
                'coupon_code'      => $coupon?->code,
                'coupon_discount'  => $totals['discount'],
                'tax'              => 0,
                'tax_rate'         => 0,
                'tax_type'         => 'percent',
                'delivery_charges' => $delivery,
                'weight'           => $weight,
                'total'            => $grandTotal,
                'paid_amount'      => 0,
                'previous_balance' => $customer ? (float) ($customer->current_balance ?? 0) : 0,
                'balance_amount'   => $grandTotal,
                'payment_method'   => $data['payment_method'],
                'payment_status'   => 'unpaid',
                'online_payment_status' => $data['payment_method'] === 'cod' ? 'cod' : 'bank_pending',
                'status'           => 'pending',
                'dispatch_method'  => $data['dispatch_method'],
                'shipping_first_name' => $data['shipping_first_name'],
                'shipping_last_name'  => $data['shipping_last_name'] ?? null,
                'shipping_phone'      => $data['shipping_phone'],
                'shipping_address1'   => $data['shipping_address1'],
                'shipping_address2'   => $data['shipping_address2'] ?? null,
                'shipping_city'       => $data['shipping_city'],
                'shipping_post_code'  => $data['shipping_post_code'] ?? null,
                'shipping_country'    => 'Pakistan',
                'order_notes_customer'=> $data['order_notes_customer'] ?? null,
                'receipt_token'       => bin2hex(random_bytes(16)),
            ]);

            foreach ($items as $row) {
                $product = $row->product;
                if (!$product) continue;
                OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $product->id,
                    'quantity'    => $row->qty,
                    'unit_price'  => $row->unit_price,
                    'total_price' => round((float) $row->qty * (float) $row->unit_price, 2),
                ]);
                if ($product->track_inventory && $branchId) {
                    $product->decrementBranchStock($branchId, (float) $row->qty);
                }
            }

            // Logged-in customer's khata grows by the unpaid amount.
            if ($customer) {
                $customer->update([
                    'current_balance' => round((float) ($customer->current_balance ?? 0) + $grandTotal, 2),
                ]);
            }

            $this->cart->clear();

            // Stash the receipt token in the session so guests can return to the
            // thank-you page after a refresh without re-authentication.
            Session::put('shop.last_guest_order_token', $order->receipt_token);

            return $order;
        });

        return redirect()->route('shop.checkout.thanks', $order)->with('shop_success', 'Order placed!');
    }

    public function thankYou(Order $order)
    {
        // Logged-in customer: must own the order. Guest: must match session token.
        $authorised = (Auth::guard('customer')->check() && (int) $order->customer_id === (int) Auth::guard('customer')->id())
                   || ($order->receipt_token && Session::get('shop.last_guest_order_token') === $order->receipt_token);

        abort_unless($authorised, 404);

        $order->load('items.product');
        return view('shop.pages.thanks', compact('order'));
    }

    private function resolveDelivery(string $dispatchMethodName, float $weight): float
    {
        $dm = DispatchMethod::where('name', $dispatchMethodName)->first();
        if (!$dm) return 0;
        $slab = DeliveryChargeSlab::active()
            ->where('dispatch_method_id', $dm->id)
            ->where('min_weight', '<=', $weight)
            ->where(function ($q) use ($weight) {
                $q->whereNull('max_weight')->orWhere('max_weight', '>=', $weight);
            })
            ->orderBy('min_weight')->first();
        return $slab ? (float) $slab->charge : 0;
    }
}
