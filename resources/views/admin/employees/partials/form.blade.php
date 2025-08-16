@php
    $inputClass = "w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm";
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block mb-1 font-medium">Name</label>
        <input type="text" name="name" value="{{ old('name', $employee?->user->name ?? '') }}" class="{{ $inputClass }}">
    </div>

    <div>
        <label class="block mb-1 font-medium">Email</label>
        <input type="email" name="email" value="{{ old('email', $employee?->user->email ?? '') }}" class="{{ $inputClass }}">
    </div>

    @if(!isset($employee))
        <div>
            <label class="block mb-1 font-medium">Password</label>
            <input type="password" name="password" class="{{ $inputClass }}">
        </div>
    @endif

    <div>
        <label class="block mb-1 font-medium">Role</label>
        <select name="role" class="{{ $inputClass }}">
            @foreach($roles as $role)
                <option value="{{ $role->name }}" @selected(old('role', $employee?->user->roles->first()->name ?? '') === $role->name)>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block mb-1 font-medium">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $employee?->phone ?? '') }}" class="{{ $inputClass }}">
    </div>

    <div>
        <label class="block mb-1 font-medium">Address</label>
        <input type="text" name="address" value="{{ old('address', $employee?->address ?? '') }}" class="{{ $inputClass }}">
    </div>

    <div>
        <label class="block mb-1 font-medium">Salary</label>
        <input type="number" name="salary" value="{{ old('salary', $employee?->salary ?? '') }}" class="{{ $inputClass }}">
    </div>

    <div>
        <label class="block mb-1 font-medium">Joining Date</label>
        <input type="date" name="joining_date" value="{{ old('joining_date', $employee?->joining_date ?? '') }}" class="{{ $inputClass }}">
    </div>
</div>
