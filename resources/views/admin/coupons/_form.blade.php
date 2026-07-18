@csrf
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Coupon code *</label>
        <input type="text" name="code" required value="{{ old('code', $coupon->code ?? '') }}" placeholder="e.g. EID500"
               class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm uppercase">
        @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Type *</label>
        <select name="type" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm">
            <option value="percent" {{ old('type', $coupon->type ?? 'percent') === 'percent' ? 'selected' : '' }}>Percent (%)</option>
            <option value="fixed" {{ old('type', $coupon->type ?? '') === 'fixed' ? 'selected' : '' }}>Fixed (Rs.)</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Value *</label>
        <input type="number" step="0.01" min="0" name="value" required value="{{ old('value', $coupon->value ?? '') }}" placeholder="e.g. 10 (% ) or 500 (Rs)"
               class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm">
        @error('value')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Min order amount</label>
        <input type="number" step="0.01" min="0" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount ?? '') }}" placeholder="0 = no minimum"
               class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Max discount (for % type)</label>
        <input type="number" step="0.01" min="0" name="max_discount" value="{{ old('max_discount', $coupon->max_discount ?? '') }}" placeholder="Optional cap in Rs"
               class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Usage limit</label>
        <input type="number" min="0" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}" placeholder="0 = unlimited"
               class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm">
        @if (isset($coupon))<p class="mt-1 text-xs text-gray-400">Used so far: {{ $coupon->used_count ?? 0 }}</p>@endif
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Starts on</label>
        <input type="date" name="starts_on" value="{{ old('starts_on', optional($coupon->starts_on ?? null)->format('Y-m-d')) }}"
               class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Expires on</label>
        <input type="date" name="expires_on" value="{{ old('expires_on', optional($coupon->expires_on ?? null)->format('Y-m-d')) }}"
               class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm">
        @error('expires_on')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
<div class="mt-4">
    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
        Active (customers can use it)
    </label>
</div>
<div class="mt-6 flex gap-3">
    <button class="px-5 py-2 bg-cyan-700 hover:bg-cyan-800 text-white text-sm font-semibold rounded-lg">
        <i class="fas fa-save"></i> Save coupon
    </button>
    <a href="{{ route('admin.coupons.index') }}" class="px-5 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
</div>
