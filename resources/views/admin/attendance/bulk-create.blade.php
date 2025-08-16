@extends('layouts.admin')

@section('title', 'Bulk Attendance Marking')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">🗓️ Bulk Attendance Marking</h2>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form method="POST" action="{{ route('admin.attendance.bulk-store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Date</label>
                    <input type="date" id="date" name="date" required
                           value="{{ old('date', now()->format('Y-m-d')) }}"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold">Employee</th>
                            <th class="px-4 py-2 text-left font-semibold">Status</th>
                            <th class="px-4 py-2 text-left font-semibold">Check In</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($employees as $employee)
                            <tr>
                                <td class="px-4 py-2">
                                    {{ $employee->user->name }}
                                    <input type="hidden" name="attendances[{{ $loop->index }}][employee_id]" value="{{ $employee->id }}">
                                </td>
                                <td class="px-4 py-2">
                                    <select name="attendances[{{ $loop->index }}][status]"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 status-select">
                                        <option value="present">Present</option>
                                        <option value="late">Late</option>
                                        <option value="on_leave">On Leave</option>
                                        <option value="half_day">Half Day</option>
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="time" name="attendances[{{ $loop->index }}][check_in]"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 check-in-time"
                                           value="09:00">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-4">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm">
                        💾 Save All
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.status-select').forEach(select => {
                select.addEventListener('change', function () {
                    const timeInput = this.closest('tr').querySelector('.check-in-time');
                    switch (this.value) {
                        case 'late':
                            timeInput.value = '10:00';
                            break;
                        case 'half_day':
                            timeInput.value = '12:00';
                            break;
                        default:
                            timeInput.value = '09:00';
                    }
                });
            });
        });
    </script>
@endsection
