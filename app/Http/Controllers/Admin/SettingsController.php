<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\DispatchMethod;
use App\Models\DeliveryChargeSlab;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('sort_order')->get();
        $dispatchMethods = DispatchMethod::with('deliverySlabs')->orderBy('sort_order')->get();
        $deliverySlabs = DeliveryChargeSlab::with('dispatchMethod')->orderBy('dispatch_method_id')->orderBy('min_weight')->get();

        return view('admin.settings.index', compact('paymentMethods', 'dispatchMethods', 'deliverySlabs'));
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

    // ── Delivery Charge Slabs ──

    public function storeDeliverySlab(Request $request)
    {
        $request->validate([
            'dispatch_method_id' => 'required|exists:dispatch_methods,id',
            'min_weight'         => 'required|numeric|min:0',
            'max_weight'         => 'required|numeric|gt:min_weight',
            'charge'             => 'required|numeric|min:0',
        ]);

        DeliveryChargeSlab::create($request->only('dispatch_method_id', 'min_weight', 'max_weight', 'charge'));

        return redirect()->route('admin.settings.index')->with('success', 'Delivery charge slab added.');
    }

    public function updateDeliverySlab(Request $request, DeliveryChargeSlab $slab)
    {
        $request->validate([
            'min_weight' => 'required|numeric|min:0',
            'max_weight' => 'required|numeric|gt:min_weight',
            'charge'     => 'required|numeric|min:0',
        ]);

        $slab->update($request->only('min_weight', 'max_weight', 'charge'));

        return redirect()->route('admin.settings.index')->with('success', 'Delivery charge slab updated.');
    }

    public function toggleDeliverySlab(DeliveryChargeSlab $slab)
    {
        $slab->update(['is_active' => !$slab->is_active]);

        return redirect()->route('admin.settings.index')->with('success', 'Delivery slab ' . ($slab->is_active ? 'enabled' : 'disabled') . '.');
    }

    public function destroyDeliverySlab(DeliveryChargeSlab $slab)
    {
        $slab->delete();

        return redirect()->route('admin.settings.index')->with('success', 'Delivery charge slab deleted.');
    }
}
