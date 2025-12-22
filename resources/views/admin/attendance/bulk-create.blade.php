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
                            <th class="px-4 py-2 text-left font-semibold">Sessions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($employees as $employee)
                            <tr>
                                <td class="px-4 py-2">
                                    {{ $employee->user->name }}
                                    <input type="hidden" name="attendances[{{ $loop->index }}][employee_id]" value="{{ $employee->id }}">
                                    <input type="hidden" name="attendances[{{ $loop->index }}][date]" value="{{ old('date', now()->format('Y-m-d')) }}">
                                </td>
                                <td class="px-4 py-2">
                                    <select name="attendances[{{ $loop->index }}][status]"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 status-select">
                                        <option value="present">Present</option>
                                        <option value="absent">Absent</option>
                                        <option value="late">Late</option>
                                        <option value="on_leave">On Leave</option>
                                        <option value="half_day">Half Day</option>
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="sessions-wrapper space-y-2">
                                        <div class="flex space-x-2 session-row">
                                            <input type="time" name="attendances[{{ $loop->index }}][sessions][0][check_in]" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white" value="09:00">
                                            <input type="time" name="attendances[{{ $loop->index }}][sessions][0][check_out]" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white" value="">
                                        </div>
                                    </div>
                                    <button type="button" class="add-session mt-2 text-blue-600 text-sm">➕ Add Session</button>
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
                    const wrapper = this.closest('tr').querySelector('.sessions-wrapper');
                    const firstSession = wrapper.querySelector('.session-row input[type="time"]');
                    switch (this.value) {
                        case 'late':
                            firstSession.value = '10:00';
                            break;
                        case 'half_day':
                            firstSession.value = '12:00';
                            break;
                        default:
                            firstSession.value = '09:00';
                    }
                });
            });

            // Add session rows dynamically
            document.querySelectorAll('.add-session').forEach(button => {
                button.addEventListener('click', function () {
                    const wrapper = this.closest('td').querySelector('.sessions-wrapper');
                    const index = wrapper.closest('tr').rowIndex - 1; // employee index
                    const sessionCount = wrapper.querySelectorAll('.session-row').length;

                    const newRow = document.createElement('div');
                    newRow.classList.add('flex', 'space-x-2', 'session-row');
                    newRow.innerHTML = `
                        <input type="time" name="attendances[${index}][sessions][${sessionCount}][check_in]" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                        <input type="time" name="attendances[${index}][sessions][${sessionCount}][check_out]" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                    `;
                    wrapper.appendChild(newRow);
                });
            });
        });
    </script>
@endsection
