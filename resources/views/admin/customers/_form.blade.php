@csrf
<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Name *</label>
        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}" required
               class="w-full border border-gray-300 rounded-md px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" value="{{ old('email', $customer->email ?? '') }}"
               class="w-full border border-gray-300 rounded-md px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
               class="w-full border border-gray-300 rounded-md px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Address</label>
        <textarea name="address" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2">{{ old('address', $customer->address ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Loyalty Points</label>
        <input type="number" name="loyalty_points" value="{{ old('loyalty_points', $customer->loyalty_points ?? 0) }}"
               class="w-full border border-gray-300 rounded-md px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Customer Type *</label>
        <select name="customer_type" required class="w-full border border-gray-300 rounded-md px-3 py-2">
            <option value="customer" {{ old('customer_type', $customer->customer_type ?? '') === 'customer' ? 'selected' : '' }}>Customer</option>
            <option value="reseller" {{ old('customer_type', $customer->customer_type ?? '') === 'reseller' ? 'selected' : '' }}>Reseller</option>
            <option value="wholesaler" {{ old('customer_type', $customer->customer_type ?? '') === 'wholesaler' ? 'selected' : '' }}>Wholesaler</option>
        </select>
    </div>
</div>

<div class="mt-6">
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        {{ $buttonText }}
    </button>
</div>
