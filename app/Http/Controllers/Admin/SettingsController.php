<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\DispatchMethod;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('sort_order')->get();
        $dispatchMethods = DispatchMethod::orderBy('sort_order')->get();

        return view('admin.settings.index', compact('paymentMethods', 'dispatchMethods'));
    }

    // ── Payment Methods ──

    public function storePaymentMethod(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:50|unique:payment_methods,name',
            'label' => 'required|string|max:50',
        ]);

        $maxOrder = PaymentMethod::max('sort_order') ?? 0;
        $validated['sort_order'] = $maxOrder + 1;

        PaymentMethod::create($validated);

        return redirect()->route('admin.settings.index')->with('success', 'Payment method added.');
    }

    public function updatePaymentMethod(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:50|unique:payment_methods,name,' . $paymentMethod->id,
            'label' => 'required|string|max:50',
        ]);

        $paymentMethod->update($validated);

        return redirect()->route('admin.settings.index')->with('success', 'Payment method updated.');
    }

    public function togglePaymentMethod(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update(['is_active' => !$paymentMethod->is_active]);

        return redirect()->route('admin.settings.index')->with('success', 'Payment method ' . ($paymentMethod->is_active ? 'enabled' : 'disabled') . '.');
    }

    public function destroyPaymentMethod(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return redirect()->route('admin.settings.index')->with('success', 'Payment method deleted.');
    }

    public function reorderPaymentMethods(Request $request)
    {
        $order = $request->input('order', []);
        foreach ($order as $index => $id) {
            PaymentMethod::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    // ── Dispatch Methods ──

    public function storeDispatchMethod(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:50|unique:dispatch_methods,name',
            'has_tracking' => 'boolean',
        ]);

        $validated['has_tracking'] = $request->boolean('has_tracking');
        $maxOrder = DispatchMethod::max('sort_order') ?? 0;
        $validated['sort_order'] = $maxOrder + 1;

        DispatchMethod::create($validated);

        return redirect()->route('admin.settings.index')->with('success', 'Dispatch method added.');
    }

    public function updateDispatchMethod(Request $request, DispatchMethod $dispatchMethod)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:50|unique:dispatch_methods,name,' . $dispatchMethod->id,
            'has_tracking' => 'boolean',
        ]);

        $validated['has_tracking'] = $request->boolean('has_tracking');
        $dispatchMethod->update($validated);

        return redirect()->route('admin.settings.index')->with('success', 'Dispatch method updated.');
    }

    public function toggleDispatchMethod(DispatchMethod $dispatchMethod)
    {
        $dispatchMethod->update(['is_active' => !$dispatchMethod->is_active]);

        return redirect()->route('admin.settings.index')->with('success', 'Dispatch method ' . ($dispatchMethod->is_active ? 'enabled' : 'disabled') . '.');
    }

    public function destroyDispatchMethod(DispatchMethod $dispatchMethod)
    {
        $dispatchMethod->delete();

        return redirect()->route('admin.settings.index')->with('success', 'Dispatch method deleted.');
    }
}
