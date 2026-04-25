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

    public function orderShow(Order $order)
    {
        abort_unless((int) $order->customer_id === (int) Auth::guard('customer')->id(), 404);
        $order->load('items.product');
        return view('shop.account.order', compact('order'));
    }
}
