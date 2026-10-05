<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $recentOrders = Order::where('customer_id', $customer->id)
            ->latest()->limit(5)->get();
        return view('shop.account.dashboard', compact('customer', 'recentOrders'));
    }

    public function profile()
    {
        $customer = Auth::guard('customer')->user();
        return view('shop.account.profile', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $data = $request->validate([
            'name'    => 'required|string|max:191',
            'email'   => 'required|email|unique:customers,email,' . $customer->id,
            'phone'   => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
        ]);
        $customer->update($data);
        return back()->with('shop_success', 'Profile updated.');
    }

    public function password()
    {
        return view('shop.account.password');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $customer = Auth::guard('customer')->user();
        if (!Hash::check($data['current_password'], $customer->password)) {
            return back()->withErrors(['current_password' => 'Current password ghalat hai.']);
        }
        $customer->update(['password' => $data['password']]);
        return back()->with('shop_success', 'Password updated.');
    }

    public function orders()
    {
        $orders = Order::where('customer_id', Auth::guard('customer')->id())
            ->latest()->paginate(10);
        return view('shop.account.orders', compact('orders'));
    }

    /**
     * Account statement / khata — the same ledger the POS shows the admin for
     * this customer: orders (bills/debits) + standalone khata payments &
     * payouts (credits), with a running balance anchored to current_balance.
     * For resellers/wholesalers it also estimates earnings (retail − paid).
     *
     * Mirrors Admin\CustomerController::khata() so the figures match exactly.
     */
    public function statement(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        // Default to all-time; allow optional date filter.
        $from = $request->input('from');
        $to   = $request->input('to');

        // Same khata rows and running balance as the admin khata screen.
        $entries = \App\Services\KhataService::entries($customer, $from, $to);
        [$entries, $openingBalance] = \App\Services\KhataService::withRunningBalance($customer, $entries, $to);
        $rows = $entries->map(function ($r) {
            $r['running'] = $r['running_balance'];
            return $r;
        })->reverse()->values()->toArray(); // newest first

        // ── Summary + reseller earnings ───────────────────────────────────
        $isReseller = in_array($customer->customer_type, ['reseller', 'wholesale'], true);
        $bills      = $entries->where('type', 'order');

        $summary = [
            'orders'      => $bills->count(),
            'business'    => (float) $bills->sum('amount'),                                   // how much purchased
            'paid'        => (float) ($bills->sum('paid') + $entries->whereIn('type', ['payment', 'offset', 'refund'])->sum('amount')),
            'outstanding' => (float) ($customer->current_balance ?? 0),                       // what's remaining (khata)
            'earnings'    => $isReseller ? \App\Services\KhataService::resellerEarnings($customer, $from, $to) : 0.0,
            'is_reseller' => $isReseller,
            'opening'     => (float) $openingBalance,
        ];

        return view('shop.account.statement', compact('customer', 'rows', 'summary', 'from', 'to'));
    }

    public function orderShow(Order $order)
    {
        abort_unless((int) $order->customer_id === (int) Auth::guard('customer')->id(), 404);
        $order->load('items.product');
        return view('shop.account.order', compact('order'));
    }

    public function points()
    {
        $customer = Auth::guard('customer')->user();
        $transactions = $customer->pointTransactions()->paginate(20);
        return view('shop.account.points', compact('customer', 'transactions'));
    }

    /**
     * Re-order a returned order (#1e): create a NEW order with a new number from
     * the same items + address, charge delivery again, and link the two orders.
     */
    public function reorder(Request $request, Order $order)
    {
        $customer = Auth::guard('customer')->user();
        abort_unless((int) $order->customer_id === (int) ($customer->id ?? 0), 404);
        abort_unless($order->status === 'returned', 400, 'Only returned orders can be re-ordered.');

        if ($order->reorderedAs()->exists()) {
            return back()->with('shop_error', 'Is order ka reorder pehle ho chuka hai.');
        }

        $order->load('items.product');
        if ($order->items->isEmpty()) {
            return back()->with('shop_error', 'Is order me koi item nahi.');
        }

        $new = \Illuminate\Support\Facades\DB::transaction(function () use ($order, $customer) {
            $subtotal = 0.0;
            $delivery = (float) ($order->delivery_charges ?? 0);

            $new = Order::create([
                'order_number'       => Order::generateOrderNumber($order->branch_id),
                'order_source'       => 'online',
                'order_type'         => 'online',
                'reorder_of_order_id'=> $order->id,
                'customer_id'        => $order->customer_id,
                'customer_email'     => $order->customer_email,
                'customer_type'      => $order->customer_type,
                'branch_id'          => $order->branch_id,
                'delivery_charges'   => $delivery,
                'weight'             => $order->weight,
                'paid_amount'        => 0,
                'previous_balance'   => (float) ($customer->current_balance ?? 0),
                'payment_method'     => $order->payment_method,
                'payment_status'     => 'unpaid',
                'online_payment_status' => 'cod' === $order->online_payment_status ? 'cod' : 'bank_pending',
                'status'             => 'pending',
                'dispatch_method'    => $order->dispatch_method,
                'shipping_first_name'=> $order->shipping_first_name,
                'shipping_last_name' => $order->shipping_last_name,
                'shipping_phone'     => $order->shipping_phone,
                'shipping_address1'  => $order->shipping_address1,
                'shipping_address2'  => $order->shipping_address2,
                'shipping_city'      => $order->shipping_city,
                'shipping_tehsil'    => $order->shipping_tehsil,
                'shipping_district'  => $order->shipping_district,
                'shipping_province'  => $order->shipping_province,
                'shipping_country'   => $order->shipping_country ?: 'Pakistan',
                'shipping_post_code' => $order->shipping_post_code,
                'subtotal'           => 0,
                'total'              => 0,
                'balance_amount'     => 0,
                'receipt_token'      => bin2hex(random_bytes(16)),
            ]);

            $packingTotal = 0.0;
            foreach ($order->items as $item) {
                $lineTotal = round((float) $item->quantity * (float) $item->unit_price, 2);
                $subtotal += $lineTotal;
                $packingTotal += (float) ($item->packing_charge ?? 0) * (float) $item->quantity;
                \App\Models\OrderItem::create([
                    'order_id'    => $new->id,
                    'product_id'  => $item->product_id,
                    'variant_id'  => $item->variant_id,
                    'variant_label' => $item->variant_label,
                    'quantity'    => $item->quantity,
                    'unit_price'  => $item->unit_price,
                    'total_price' => $lineTotal,
                    ...\App\Services\KhataService::itemSnapshot($item->product),
                    'packing_charge' => (float) ($item->packing_charge ?? 0),
                    'packing_label'  => $item->packing_label,
                ]);
                if ($item->product && $item->product->track_inventory && $new->branch_id) {
                    $item->product->decrementBranchStock($new->branch_id, (float) $item->quantity, $item->variant_id);
                }
            }
            $packingTotal = round($packingTotal, 2);

            // Apply tax the same way a fresh order does (#1e correctness).
            $tax   = shop_tax_amount($subtotal + $delivery, $order->online_payment_status === 'cod');
            $total = round($subtotal + $delivery + $tax + $packingTotal, 2);
            $new->update(['subtotal' => $subtotal, 'tax' => $tax, 'packing_total' => $packingTotal, 'total' => $total, 'balance_amount' => $total]);

            if ($customer) {
                $customer->update(['current_balance' => round((float) ($customer->current_balance ?? 0) + $total, 2)]);
            }

            $new->recordStatus('pending', 'Re-order of returned order ' . $order->order_number);
            $order->recordStatus($order->status, 'Re-ordered as ' . $new->order_number);

            return $new;
        });

        return redirect()->route('shop.account.order', $new)->with('shop_success', 'Naya order ' . $new->order_number . ' ban gaya (returned order ' . $order->order_number . ' se).');
    }

    /** Pay-a-pending-balance form (#1b). */
    public function payForm()
    {
        $customer = Auth::guard('customer')->user();
        $recent = \App\Models\AccountRequest::where('customer_id', $customer->id)
            ->where('type', 'payment')->latest()->limit(10)->get();
        return view('shop.account.pay', compact('customer', 'recent'));
    }

    public function paySubmit(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'amount'       => 'required|numeric|min:1',
            'sender_name'  => 'nullable|string|max:191',
            'sender_bank'  => 'nullable|string|max:191',
            'reference'    => 'nullable|string|max:191',
            'proof'        => 'required|image|mimes:png,jpg,jpeg,webp|max:5120',
        ]);

        $proofPath = $request->file('proof')->store('account-requests', 'public');

        \App\Models\AccountRequest::create([
            'customer_id' => $customer->id,
            'type'        => 'payment',
            'amount'      => $data['amount'],
            'sender_name' => $data['sender_name'] ?? $customer->name,
            'sender_bank' => $data['sender_bank'] ?? null,
            'reference'   => $data['reference'] ?? null,
            'proof_path'  => $proofPath,
            'status'      => 'new',
        ]);

        return redirect()->route('shop.account')->with('shop_success', 'Payment submit ho gaya — admin approve karne ke baad aap ke khate me adjust ho jayega.');
    }

    /** Withdraw-a-credit form (#1c). */
    public function withdrawForm()
    {
        $customer = Auth::guard('customer')->user();
        $credit = max(0, -1 * (float) ($customer->current_balance ?? 0));
        $recent = \App\Models\AccountRequest::where('customer_id', $customer->id)
            ->where('type', 'withdrawal')->latest()->limit(10)->get();
        return view('shop.account.withdraw', compact('customer', 'credit', 'recent'));
    }

    public function withdrawSubmit(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $credit = max(0, -1 * (float) ($customer->current_balance ?? 0));

        $data = $request->validate([
            'amount'         => 'required|numeric|min:1|max:' . ($credit ?: 0),
            'account_title'  => 'required|string|max:191',
            'account_number' => 'required|string|max:100',
            'bank_name'      => 'required|string|max:191',
        ], [
            'amount.max' => 'Aap sirf apne available credit (Rs ' . number_format($credit, 0) . ') tak withdraw kar sakte hain.',
        ]);

        \App\Models\AccountRequest::create([
            'customer_id'    => $customer->id,
            'type'           => 'withdrawal',
            'amount'         => $data['amount'],
            'account_title'  => $data['account_title'],
            'account_number' => $data['account_number'],
            'bank_name'      => $data['bank_name'],
            'status'         => 'new',
        ]);

        return redirect()->route('shop.account')->with('shop_success', 'Withdrawal request submit ho gayi — admin approve karke aap ke account me bhej dega.');
    }

    /**
     * Unified khata history (client #10 / #12): every payment & withdrawal request
     * the customer made, with both the customer's and the admin's screenshots and
     * the approval status — so they can see their khata activity end to end.
     */
    public function history()
    {
        $customer = Auth::guard('customer')->user();
        $requests = \App\Models\AccountRequest::where('customer_id', $customer->id)
            ->latest()->paginate(20);
        return view('shop.account.history', compact('customer', 'requests'));
    }

    /** Customer attaches a payment screenshot to one of their own orders. */
    public function uploadProof(Request $request, Order $order)
    {
        abort_unless((int) $order->customer_id === (int) Auth::guard('customer')->id(), 404);

        $data = $request->validate([
            'payment_sender_name'   => 'nullable|string|max:191',
            'payment_sender_bank'   => 'nullable|string|max:191',
            'payment_sender_amount' => 'nullable|numeric|min:0',
            'payment_proof'         => 'required|image|mimes:png,jpg,jpeg,webp|max:4096',
        ]);

        if ($order->payment_proof_path && \Storage::disk('public')->exists($order->payment_proof_path)) {
            \Storage::disk('public')->delete($order->payment_proof_path);
        }

        $order->update([
            'payment_proof_path'    => $request->file('payment_proof')->store('payment-proofs', 'public'),
            'payment_sender_name'   => $data['payment_sender_name'] ?? $order->payment_sender_name,
            'payment_sender_bank'   => $data['payment_sender_bank'] ?? $order->payment_sender_bank,
            'payment_sender_amount' => ($data['payment_sender_amount'] ?? '') !== '' ? $data['payment_sender_amount'] : $order->payment_sender_amount,
            'online_payment_status' => $order->online_payment_status === 'cod' ? $order->online_payment_status : 'proof_submitted',
        ]);

        return back()->with('shop_success', 'Payment proof submitted — we will verify and confirm shortly.');
    }

    /**
     * Let a customer confirm receipt of their own parcel by marking it delivered.
     * Only allowed once the order is already on its way (dispatched/shipped), so
     * a customer can only CONFIRM delivery — not move an order backwards or skip
     * fulfilment steps. Storefront-only; POS orders are never touched here.
     */
    public function markDelivered(Request $request, Order $order)
    {
        abort_unless((int) $order->customer_id === (int) Auth::guard('customer')->id(), 404);

        if (! in_array($order->status, ['dispatched', 'shipped'], true)) {
            return back()->with('shop_error', 'This order can’t be marked delivered right now.');
        }

        \DB::transaction(function () use ($order) {
            $order->update(['status' => 'delivered']);
            $order->recordStatus('delivered', 'Confirmed delivered by customer');

            // Loyalty points on delivery — once per order (same guard as admin).
            if ($order->customer) {
                $already = \App\Models\PointTransaction::where('order_id', $order->id)
                    ->where('type', 'earn_order')->exists();
                $points = shop_order_points($order->total);
                if (! $already && $points > 0) {
                    $order->customer->awardPoints($points, 'earn_order', "Order {$order->order_number}", $order->id);
                }
            }
        });

        return back()->with('shop_success', 'Thank you — your order is marked delivered.');
    }
}
