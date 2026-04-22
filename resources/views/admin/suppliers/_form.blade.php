@csrf
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Basic Information --}}
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-800 border-b pb-2">Basic Information</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}" required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Supplier name">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Company Name</label>
                <div class="relative">
                    <input type="text" name="company_name" value="{{ old('company_name', $supplier->company_name ?? '') }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g. ABC Trading Co.">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500">🏢</span>
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500">Leave blank for individual suppliers</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Phone <span class="text-red-500">*</span></label>
                <input type="text" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}" required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="+92 300 1234567">
            </div>
        </div>

        {{-- Additional Information --}}
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-800 border-b pb-2">Additional Information</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $supplier->email ?? '') }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="supplier@example.com">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <textarea name="address" rows="5"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Full address">{{ old('address', $supplier->address ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="mt-8 pt-6 border-t border-gray-200 flex justify-between">
    <a href="{{ route('suppliers.index') }}"
        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
        Cancel
    </a>
    <button type="submit"
        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        {{ $buttonText }}
    </button>
</div>
