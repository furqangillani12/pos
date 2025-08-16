@extends('layouts.admin')

@section('title', 'Mark Attendance')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800">📝 Mark Attendance</h2>

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.attendance.store') }}" class="space-y-6">
            @csrf

            <!-- Employee & Date -->
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                    <select name="employee_id" id="employee_id" required
                            class="w-full border-gray-300 rounded px-3 py-2 text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->user->name }} ({{ $employee->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" id="date" required
                           value="{{ old('date', now()->format('Y-m-d')) }}"
                           class="w-full border-gray-300 rounded px-3 py-2 text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Time & Status -->
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label for="check_in" class="block text-sm font-medium text-gray-700 mb-1">Check-In Time</label>
                    <input type="time" name="check_in" id="check_in" required
                           value="{{ old('check_in', '09:00') }}"
                           class="w-full border-gray-300 rounded px-3 py-2 text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" required
                            class="w-full border-gray-300 rounded px-3 py-2 text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>Present</option>
                        <option value="late" {{ old('status') == 'late' ? 'selected' : '' }}>Late</option>
                        <option value="on_leave" {{ old('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="half_day" {{ old('status') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full border-gray-300 rounded px-3 py-2 text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="text-right">
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 text-sm font-semibold shadow">
                    💾 Save Attendance
                </button>
            </div>
        </form>
    </div>
@endsection
