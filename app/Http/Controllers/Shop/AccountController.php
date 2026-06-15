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

        $ordersQuery = $customer->orders()
            ->with('items.product:id,name,price,sale_price')
            ->where('status', '!=', 'cancelled')
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to,   fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->orderBy('created_at');
        $orders = $ordersQuery->get();

        $khataPayments = \App\Models\Payment::where('customer_id', $customer->id)
            ->whereIn('payment_type', ['khata', 'khata_payout', 'khata_offset'])
            ->when($from, fn ($q) => $q->whereDate('payment_date', '>=', $from))
            ->when($to,   fn ($q) => $q->whereDate('payment_date', '<=', $to))
            ->orderBy('payment_date')
            ->get();

        // ── Merge into one timeline ────────────────────────────────────────
        $txns = collect();
        foreach ($orders as $order) {
            $paid = ($order->paid_amount == 0 && $order->balance_amount == 0)
                ? $order->total : $order->paid_amount;
            $txns->push([
                'type'        => 'order',
                'date'        => $order->created_at,
                'reference'   => $order->order_number,
                'order'       => $order,
                'amount'      => (float) $order->total,   // debit (bill)
                'paid'        => (float) $paid,
                'items_count' => $order->items->count(),
                'channel'     => $order->order_source === 'online' ? 'Website' : 'In-store',
                'running'     => 0.0,
            ]);
        }
        foreach ($khataPayments as $p) {
            $type = match ($p->payment_type) {
                'khata_payout' => 'payout',
                'khata_offset' => 'offset',
                default        => 'payment',
            };
            $txns->push([
                'type'        => $type,
                'date'        => $p->payment_date,
                'reference'   => $p->payment_number ?? $p->reference_number,
                'order'       => null,
                'amount'      => (float) $p->amount,
                'paid'        => (float) $p->amount,
                'items_count' => 0,
                'channel'     => $p->payment_method,
                'running'     => 0.0,
            ]);
        }

        $txns = $txns->sortBy('date')->values()->toArray();

        // ── Running balance anchored to current_balance ───────────────────
        $running = (float) ($customer->current_balance ?? 0);
        foreach ($txns as $t) {
            if ($t['type'] === 'order')       $running -= ($t['amount'] - $t['paid']);
            elseif ($t['type'] === 'payout')  $running -= $t['amount'];
            else                              $running += $t['amount'];
        }
        $openingBalance = $running;
        foreach ($txns as $i => $t) {
            if ($t['type'] === 'order')       $running += ($t['amount'] - $t['paid']);
            elseif ($t['type'] === 'payout')  $running += $t['amount'];
            else                              $running -= $t['amount'];
            $txns[$i]['running'] = $running;
        }
        $rows = array_reverse($txns); // newest first

        // ── Summary + reseller earnings ───────────────────────────────────
        $isReseller   = in_array($customer->customer_type, ['reseller', 'wholesale'], true);
        $totalBilled  = $orders->sum('total');
        $totalPaidOrd = $orders->sum(fn ($o) => ($o->paid_amount == 0 && $o->balance_amount == 0) ? $o->total : $o->paid_amount);
        $totalKhataPmt= $khataPayments->where('payment_type', 'khata')->sum('amount');

        $earnings = 0.0;
        if ($isReseller) {
            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    $retail   = (float) ($item->product->price ?: $item->product->sale_price ?: 0);
                    $paidUnit = (float) $item->unit_price;
                    if ($retail > $paidUnit) {
                        $earnings += ($retail - $paidUnit) * (float) $item->quantity;
                    }
                }
            }
        }

        $summary = [
            'orders'      => $orders->count(),
            'business'    => (float) $totalBilled,                       // how much purchased
            'paid'        => (float) ($totalPaidOrd + $totalKhataPmt),   // total paid
            'outstanding' => (float) ($customer->current_balance ?? 0),  // what's remaining (khata)
            'earnings'    => $earnings,
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
}
