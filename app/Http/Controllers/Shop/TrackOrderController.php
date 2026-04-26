<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class TrackOrderController extends Controller
{
    public function show()
    {
        return view('shop.pages.track');
    }

    /**
     * Look up an order by order_number + (email OR phone).
     * Either match must be on the order itself — guests don't have accounts.
     */
    public function find(Request $request)
    {
        $data = $request->validate([
            'order_number' => 'required|string|max:50',
            'contact'      => 'required|string|max:191',
        ]);

        $contact = trim($data['contact']);
        $isEmail = filter_var($contact, FILTER_VALIDATE_EMAIL) !== false;

        $order = Order::where('order_number', trim($data['order_number']))
            ->where(function ($q) use ($contact, $isEmail) {
                if ($isEmail) {
                    $q->where('customer_email', $contact);
                } else {
                    // Match by shipping phone (digits-only comparison so spacing differences forgive)
                    $digits = preg_replace('/\D+/', '', $contact);
                    $q->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(shipping_phone,' ',''),'-',''),'(',''),')','') = ?", [$digits])
                      ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(COALESCE((SELECT phone FROM customers WHERE customers.id=orders.customer_id),''),' ',''),'-',''),'(',''),')','') = ?", [$digits]);
                }
            })
            ->first();

        if (!$order) {
            return back()->withInput()->with('shop_error', 'No order found with those details. Double-check your order number and email/phone.');
        }

        return redirect()->route('shop.track.view', ['token' => $order->receipt_token]);
    }

    /**
     * Public, token-based view of the order — works for both guests and members.
     * The receipt_token is given to the customer in the thank-you confirmation.
     */
    public function view(string $token)
    {
        $order = Order::where('receipt_token', $token)->firstOrFail();
        $order->load('items.product');
        return view('shop.pages.track-result', compact('order'));
    }
}
