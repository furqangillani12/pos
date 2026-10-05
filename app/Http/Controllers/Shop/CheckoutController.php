<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\DispatchMethod;
use App\Models\DeliveryChargeSlab;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Services\Shop\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index()
    {
        $items = $this->cart->items();
        if ($items->isEmpty()) return redirect()->route('shop.cart')->with('shop_error', 'Your cart is empty.');

        $totals = $this->cart->totals();
        $coupon = $this->cart->activeCoupon();
        $customer = Auth::guard('customer')->user();
        $isGuest = !$customer;

        // Only methods the admin has chosen to show on the website.
        $dispatchMethods = DispatchMethod::onWebsite()->get();
        $paymentMethods  = PaymentMethod::onWebsite()->get();

        $weight = $items->sum(fn ($i) => (float) ($i->product?->weight ?? 0) * (float) $i->qty);
        // Packing charges preview (client #1) — same figure that will be billed.
        $packingTotal = round($items->sum(fn ($i) => (float) ($i->product?->packing_charge ?? 0) * (float) $i->qty), 2);

        // A cart holding products from several branches is placed as one order
        // per branch (see place()), each with its own delivery charge. Send the
        // per-branch figures so the form's live total matches what is billed.
        $branchNames = \App\Models\Branch::pluck('name', 'id');
        $groups = $this->branchGroups($items)->map(function ($rows, $branchId) use ($dispatchMethods, $branchNames) {
            $w = $rows->sum(fn ($i) => (float) ($i->product?->weight ?? 0) * (float) $i->qty);
            $charges = [];
            foreach ($dispatchMethods as $dm) {
                $charges[$dm->name] = $this->resolveDelivery($dm->name, $w);
            }
            return [
                'name'    => $branchNames[$branchId] ?? 'Store',
                'count'   => (int) $rows->sum('qty'),
                'sub'     => round($rows->sum(fn ($i) => (float) $i->qty * (float) $i->unit_price), 2),
                'charges' => $charges,
            ];
        })->values();

        // Live delivery charge per dispatch method (summed across branch orders),
        // so the form can show "Rs. X" the moment a method is selected.
        $deliveryCharges = [];
        foreach ($dispatchMethods as $dm) {
            $deliveryCharges[$dm->name] = round($groups->sum(fn ($g) => $g['charges'][$dm->name]), 2);
        }

        $provinces = config('pk_geo.provinces', []);

        // Points redemption (#B): how many points the logged-in customer holds
        // and what one is worth, so the form can offer "use my points".
        $pointsBalance = (int) ($customer->loyalty_points ?? 0);
        $pointValue    = shop_point_value();
        $afterCoupon   = max(0, $totals['subtotal'] - $totals['discount']);
        $maxRedeemable = $customer ? shop_max_redeemable_points($pointsBalance, $afterCoupon) : 0;

        return view('shop.pages.checkout', compact(
            'items', 'totals', 'coupon', 'customer', 'isGuest',
            'dispatchMethods', 'paymentMethods', 'deliveryCharges', 'weight', 'provinces',
            'pointsBalance', 'pointValue', 'maxRedeemable', 'packingTotal', 'groups'
        ));
    }

    /**
     * Look up a previously used shipping address by phone, so returning buyers
     * can auto-fill the form. Returns the most recent matching online order.
     */
    public function lookup(Request $request)
    {
        $phone  = preg_replace('/\D+/', '', (string) $request->input('phone'));
        if (strlen($phone) < 7) return response()->json(['ok' => false]);

        $last10 = substr($phone, -10);
        $order = Order::where('order_source', 'online')
            ->whereNotNull('shipping_address1')
            ->whereRaw("RIGHT(REPLACE(REPLACE(REPLACE(shipping_phone,'+',''),'-',''),' ',''), 10) = ?", [$last10])
            ->latest('id')
            ->first();

        if (!$order) return response()->json(['ok' => false]);

        return response()->json([
            'ok'      => true,
            'address' => [
                'shipping_first_name' => $order->shipping_first_name,
                'shipping_last_name'  => $order->shipping_last_name,
                'shipping_address1'   => $order->shipping_address1,
                'shipping_address2'   => $order->shipping_address2,
                'shipping_city'       => $order->shipping_city,
                'shipping_tehsil'     => $order->shipping_tehsil,
                'shipping_district'   => $order->shipping_district,
                'shipping_province'   => $order->shipping_province,
                'shipping_country'    => $order->shipping_country,
                'shipping_post_code'  => $order->shipping_post_code,
            ],
        ]);
    }

    public function place(Request $request)
    {
        $isGuest = !Auth::guard('customer')->check();

        $dispatchNames = DispatchMethod::onWebsite()->pluck('name')->all();
        $paymentNames  = PaymentMethod::onWebsite()->pluck('name')->all();

        $data = $request->validate([
            'shipping_first_name'  => 'required|string|max:191',
            'shipping_last_name'   => 'nullable|string|max:191',
            'shipping_phone'       => 'required|string|max:30',
            'shipping_address1'    => 'required|string|max:500',
            'shipping_address2'    => 'nullable|string|max:500',
            'shipping_city'        => 'nullable|string|max:100',
            'shipping_tehsil'      => 'nullable|string|max:100',
            'shipping_district'    => 'nullable|string|max:100',
            'shipping_province'    => 'nullable|string|max:100',
            'shipping_country'     => 'required|string|max:100',
            'shipping_post_code'   => 'nullable|string|max:20',
            'dispatch_method'      => ['required', 'string', 'max:100', Rule::in($dispatchNames)],
            'payment_method'       => ['required', 'string', 'max:100', Rule::in($paymentNames)],
            'order_notes_customer' => 'nullable|string|max:1000',
            'email'                => 'required|email|max:191',
            'redeem_points'        => 'nullable|integer|min:0',
            // Optional bank-transfer proof submitted at checkout.
            'payment_sender_name'  => 'nullable|string|max:191',
            'payment_sender_bank'  => 'nullable|string|max:191',
            'payment_sender_amount'=> 'nullable|numeric|min:0',
            'payment_proof'        => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            // Optional reseller "From" address printed on the dispatch slip.
            'from_name'            => 'nullable|string|max:191',
            'from_phone'           => 'nullable|string|max:30',
            'from_address'         => 'nullable|string|max:500',
        ]);

        $items = $this->cart->items();
        if ($items->isEmpty()) return redirect()->route('shop.cart')->with('shop_error', 'Your cart is empty.');

        $customer = Auth::guard('customer')->user();
        $totals   = $this->cart->totals();
        $coupon   = $this->cart->activeCoupon();

        // Points redemption (#B) — logged-in customers only, capped so the points
        // discount can never exceed the after-coupon subtotal. Applied like a
        // discount, so (as in the POS) it also lowers the taxable base.
        $afterCoupon    = max(0, $totals['subtotal'] - $totals['discount']);
        $redeemPoints   = 0;
        if ($customer && shop_point_value() > 0) {
            $requested      = (int) $request->input('redeem_points', 0);
            $maxRedeemable  = shop_max_redeemable_points((int) ($customer->loyalty_points ?? 0), $afterCoupon);
            $redeemPoints   = max(0, min($requested, $maxRedeemable));
        }

        $paymentModel = PaymentMethod::where('name', $data['payment_method'])->first();
        // Detect COD robustly (client: COD orders must always go through). Trust the
        // is_cod flag, but also treat a method whose name/label clearly says COD /
        // "cash on delivery" as COD, so a mis-configured flag can never block a COD sale.
        $pmName  = strtolower(trim((string) ($paymentModel?->name ?? '')));
        $pmLabel = strtolower(trim((string) ($paymentModel?->label ?? '')));
        $isCod = (bool) ($paymentModel?->is_cod)
            || in_array($pmName, ['cod', 'cash on delivery', 'cash_on_delivery', 'cash-on-delivery'], true)
            || str_contains($pmLabel, 'cod')
            || str_contains($pmLabel, 'cash on delivery');

        // Payment receipt is mandatory for every non-COD method (client P0) — COD
        // is the only method that may be placed without a receipt.
        if (! $isCod && ! $request->hasFile('payment_proof')) {
            return back()->withInput()->withErrors([
                'payment_proof' => 'Please attach your payment receipt (screenshot) for online payment. Cash on Delivery does not need it.',
            ]);
        }

        // Multi-branch POS: every branch must only see (and account for) its own
        // products, so the cart is placed as one order per owning branch. The
        // coupon and points discounts are shared out in proportion to each
        // branch's subtotal (last branch takes the rounding remainder); delivery,
        // packing and tax are worked out per branch order.
        $groups     = $this->branchGroups($items);
        if ($groups->isEmpty()) return redirect()->route('shop.cart')->with('shop_error', 'Your cart is empty.');

        // Colour / size items: the chosen option must still exist and be in stock.
        foreach ($items as $row) {
            if (!$row->product?->has_variants) continue;
            $v = $row->variant;
            if (!$v || !$v->is_active) {
                return redirect()->route('shop.cart')->with('shop_error', "Please choose a colour / size again for {$row->product->name}.");
            }
            if ($row->product->track_inventory) {
                $need = (float) $items->where('variant_id', $v->id)->sum('qty');
                if ($need > (float) $v->stock) {
                    return redirect()->route('shop.cart')->with('shop_error', "{$row->product->name} ({$v->label}): only " . (int) max(0, $v->stock) . ' left.');
                }
            }
        }
        $lastKey    = $groups->keys()->last();
        $couponLeft = (float) $totals['discount'];
        $pointsLeft = $redeemPoints;
        $plans = [];
        foreach ($groups as $branchId => $rows) {
            $isLast = $branchId === $lastKey;
            $sub    = round($rows->sum(fn ($i) => (float) $i->qty * (float) $i->unit_price), 2);
            $share  = $totals['subtotal'] > 0 ? $sub / $totals['subtotal'] : 0;

            $couponPart = $isLast ? round($couponLeft, 2) : round($totals['discount'] * $share, 2);
            $couponLeft -= $couponPart;
            $pointsPart = $isLast ? $pointsLeft : (int) floor($redeemPoints * $share);
            $pointsLeft -= $pointsPart;
            $pointsDiscount = shop_points_to_rupees($pointsPart);

            $weight   = $rows->sum(fn ($i) => (float) ($i->product?->weight ?? 0) * (float) $i->qty);
            $delivery = $this->resolveDelivery($data['dispatch_method'], $weight);
            // Packing charges (client #1): per-unit charge on fragile items, added to the bill.
            $packing  = round($rows->sum(fn ($i) => (float) ($i->product?->packing_charge ?? 0) * (float) $i->qty), 2);

            // Tax / government charges — exclusive, on (subtotal − discount + delivery).
            // Driven by the storefront tax setting (percent, or amount slabs for fixed);
            // may be limited to COD orders (client #8).
            $afterDiscount = max(0, $sub - $couponPart - $pointsDiscount);
            $tax           = shop_tax_amount($afterDiscount + $delivery, $isCod);
            // Packing is a pass-through charge added after tax (not taxed).
            $total         = max(0, $afterDiscount + $tax + $delivery + $packing);

            $plans[] = compact('branchId', 'rows', 'sub', 'couponPart', 'pointsPart', 'pointsDiscount', 'weight', 'delivery', 'packing', 'tax', 'total');
        }

        // Optional payment screenshot.
        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        }
        // Empty numeric inputs arrive as '' — normalise to null for the decimal column.
        $data['payment_sender_amount'] = ($data['payment_sender_amount'] ?? '') === '' ? null : $data['payment_sender_amount'];

        $hasProof = $proofPath || !empty($data['payment_sender_name']) || $data['payment_sender_amount'] !== null;

        $orders = DB::transaction(function () use ($plans, $customer, $data, $totals, $coupon, $isCod, $proofPath, $hasProof) {
            $orders  = collect();
            $balance = $customer ? (float) ($customer->current_balance ?? 0) : 0;

            foreach ($plans as $plan) {
                $branchId = $plan['branchId'];

                $order = Order::create([
                    'order_number'     => Order::generateOrderNumber($branchId),
                    'order_source'     => 'online',
                    'order_type'       => 'online',
                    'customer_id'      => $customer?->id,
                    'customer_email'   => $data['email'],
                    'customer_type'    => $customer?->customer_type ?? 'customer',
                    'user_id'          => null,
                    'branch_id'        => $branchId,
                    'subtotal'         => $plan['sub'],
                    'discount'         => $plan['couponPart'],
                    'coupon_code'      => $plan['couponPart'] > 0 ? $coupon?->code : null,
                    'coupon_discount'  => $plan['couponPart'],
                    'points_redeemed'  => $plan['pointsPart'],
                    'points_discount'  => $plan['pointsDiscount'],
                    'tax'              => $plan['tax'],
                    'tax_rate'         => $totals['tax_rate'],
                    'tax_type'         => $totals['tax_type'],
                    'delivery_charges' => $plan['delivery'],
                    'packing_total'    => $plan['packing'],
                    'weight'           => $plan['weight'],
                    'total'            => $plan['total'],
                    'paid_amount'      => 0,
                    'previous_balance' => $balance,
                    'balance_amount'   => $plan['total'],
                    'payment_method'   => $data['payment_method'],
                    'payment_status'   => 'unpaid',
                    'online_payment_status' => $isCod ? 'cod' : ($hasProof ? 'proof_submitted' : 'bank_pending'),
                    'status'           => 'pending',
                    'dispatch_method'  => $data['dispatch_method'],
                    'shipping_first_name' => $data['shipping_first_name'],
                    'shipping_last_name'  => $data['shipping_last_name'] ?? null,
                    'shipping_phone'      => $data['shipping_phone'],
                    'shipping_address1'   => $data['shipping_address1'],
                    'shipping_address2'   => $data['shipping_address2'] ?? null,
                    'shipping_city'       => $data['shipping_city'] ?? null,
                    'shipping_tehsil'     => $data['shipping_tehsil'] ?? null,
                    'shipping_district'   => $data['shipping_district'] ?? null,
                    'shipping_province'   => $data['shipping_province'] ?? null,
                    'shipping_country'    => $data['shipping_country'] ?: 'Pakistan',
                    'shipping_post_code'  => $data['shipping_post_code'] ?? null,
                    'payment_proof_path'     => $proofPath,
                    'payment_sender_name'    => $data['payment_sender_name'] ?? null,
                    'payment_sender_bank'    => $data['payment_sender_bank'] ?? null,
                    'payment_sender_amount'  => $data['payment_sender_amount'] ?? null,
                    'from_name'    => $data['from_name'] ?? null,
                    'from_phone'   => $data['from_phone'] ?? null,
                    'from_address' => $data['from_address'] ?? null,
                    'order_notes_customer'=> $data['order_notes_customer'] ?? null,
                    'receipt_token'       => bin2hex(random_bytes(16)),
                ]);

                foreach ($plan['rows'] as $row) {
                    $product = $row->product;
                    if (!$product) continue;
                    OrderItem::create([
                        'order_id'    => $order->id,
                        'product_id'  => $product->id,
                        'variant_id'  => $row->variant_id,
                        'variant_label' => $row->variant?->label,
                        'quantity'    => $row->qty,
                        'unit_price'  => $row->unit_price,
                        'total_price' => round((float) $row->qty * (float) $row->unit_price, 2),
                        ...\App\Services\KhataService::itemSnapshot($product, $row->variant),
                        'packing_charge' => (float) ($product->packing_charge ?? 0),
                        'packing_label'  => ($product->packing_charge ?? 0) > 0 ? ($product->packing_label ?: 'Packing charges') : null,
                    ]);
                    if ($product->track_inventory && $branchId) {
                        $product->decrementBranchStock($branchId, (float) $row->qty, $row->variant_id);
                    }
                }

                if ($customer) {
                    $balance = round($balance + $plan['total'], 2);
                    $customer->update(['current_balance' => $balance]);

                    // Deduct redeemed points (#B) and log the transaction.
                    if ($plan['pointsPart'] > 0) {
                        $customer->awardPoints(-$plan['pointsPart'], 'redeem_order', "Redeemed on order {$order->order_number}", $order->id);
                    }
                }

                $orders->push($order);
            }

            // Tell each branch its order was part of a combined checkout, so a single
            // bank transfer / proof covering all of them is not mistaken for overpayment.
            foreach ($orders as $order) {
                $others = $orders->where('id', '!=', $order->id)->pluck('order_number');
                $order->recordStatus('pending', $others->isEmpty()
                    ? 'Order placed'
                    : 'Order placed (website checkout split by branch; also: ' . $others->implode(', ') . ')');
            }

            $this->cart->clear();
            Session::put('shop.last_guest_order_token', $orders->first()->receipt_token);
            Session::put('shop.last_checkout_tokens', $orders->pluck('receipt_token')->all());

            return $orders;
        });

        foreach ($orders as $order) {
            // Order-received confirmation email (safe no-op if no email / mail fails).
            \App\Mail\OrderStatusMail::dispatchFor($order, 'placed');
            // Heads-up alert to the store team so they can action it quickly (#3).
            \App\Mail\NewOrderAdminMail::dispatchFor($order);
        }

        return redirect()->route('shop.checkout.thanks', $orders->first())
            ->with('shop_success', $orders->count() > 1 ? "{$orders->count()} orders placed!" : 'Order placed!');
    }

    public function thankYou(Order $order)
    {
        $tokens = (array) Session::get('shop.last_checkout_tokens', []);
        $authorised = (Auth::guard('customer')->check() && (int) $order->customer_id === (int) Auth::guard('customer')->id())
                   || ($order->receipt_token && Session::get('shop.last_guest_order_token') === $order->receipt_token)
                   || ($order->receipt_token && in_array($order->receipt_token, $tokens, true));

        abort_unless($authorised, 404);

        $order->load('items.product');

        // Every order from the same (branch-split) checkout, for the summary.
        $checkoutOrders = in_array($order->receipt_token, $tokens, true)
            ? Order::whereIn('receipt_token', $tokens)->orderBy('id')->get()
            : collect([$order]);

        return view('shop.pages.thanks', compact('order', 'checkoutOrders'));
    }

    /**
     * Cart rows grouped by the branch that owns each product (the product's
     * current branch, falling back to the branch stamped on the cart row, then
     * the first branch). Keyed by branch id, in ascending order.
     */
    private function branchGroups($items)
    {
        $fallback = \App\Models\Branch::query()->value('id');

        return $items->filter(fn ($i) => $i->product)
            ->groupBy(fn ($i) => (int) ($i->product->branch_id ?? $i->branch_id ?? $fallback))
            ->sortKeys();
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
