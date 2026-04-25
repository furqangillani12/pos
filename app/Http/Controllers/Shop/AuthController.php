<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\Shop\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function showLogin()    { return view('shop.auth.login'); }
    public function showRegister() { return view('shop.auth.register'); }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'remember' => 'sometimes|boolean',
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::guard('customer')->attempt(['email' => $data['email'], 'password' => $data['password']], $remember)) {
            return back()->withErrors(['email' => 'Email ya password ghalat hai (incorrect).'])->withInput();
        }

        $customer = Auth::guard('customer')->user();
        $customer->update(['last_login_at' => now()]);
        $this->cart->mergeGuestIntoCustomer($customer->id);

        $request->session()->regenerate();
        return redirect()->intended(route('shop.account'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:191',
            'email'    => 'required|email|unique:customers,email',
            'phone'    => 'nullable|string|max:30',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $customer = Customer::create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'phone'          => $data['phone'] ?? null,
            'password'       => $data['password'],
            'customer_type'  => 'customer',
            'loyalty_points' => 0,
            'credit_enabled' => false,
            'credit_limit'   => 0,
            'current_balance'=> 0,
            'credit_due_days'=> 30,
            'barcode'        => Customer::generateBarcode(),
        ]);

        Auth::guard('customer')->login($customer);
        $this->cart->mergeGuestIntoCustomer($customer->id);

        return redirect()->route('shop.account')->with('shop_success', 'Welcome to Almufeed!');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('shop.home');
    }
}
