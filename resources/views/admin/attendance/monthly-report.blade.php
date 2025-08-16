@extends('layouts.admin')

@section('title', 'Monthly Attendance Report')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h2 class="text-2xl font-bold mb-4 md:mb-0">📅 Monthly Attendance Report</h2>
            <form method="GET" class="flex space-x-2 items-center">
                <input type="month" name="month" class="border rounded px-3 py-2 shadow-sm focus:ring focus:ring-blue-200"
                       value="{{ $month }}" max="{{ now()->format('Y-m') }}">
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Generate
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-1 text-gray-800 dark:text-gray-100">
                    {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-300">📌 Total Working Days: <strong>{{ $workingDays }}</strong></p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-left text-sm font-semibold text-gray-700 dark:text-gray-100">
                    <tr>
                        <th class="px-4 py-2">Employee</th>
                        <th class="px-4 py-2 text-center">Present</th>
                        <th class="px-4 py-2 text-center">Late</th>
                        <th class="px-4 py-2 text-center">On Leave</th>
                        <th class="px-4 py-2 text-center">Absent</th>
                        <th class="px-4 py-2">Attendance %</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    @foreach($employees as $employee)
                        @php
                            $present = $employee->attendances->where('status', 'present')->count();
                            $late = $employee->attendances->where('status', 'late')->count();
                            $onLeave = $employee->attendances->where('status', 'on_leave')->count();
                            $absent = $workingDays - ($present + $late + $onLeave);
                            $attendancePercent = round(($present / $workingDays) * 100, 2);
                            $progressColor = $attendancePercent > 90 ? 'bg-green-500' : ($attendancePercent > 70 ? 'bg-yellow-500' : 'bg-red-500');
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">
                                {{ $employee->user->name }}
                            </td>
                            <td class="px-4 py-3 text-center">{{ $present }}</td>
                            <td class="px-4 py-3 text-center">{{ $late }}</td>
                            <td class="px-4 py-3 text-center">{{ $onLeave }}</td>
                            <td class="px-4 py-3 text-center">{{ $absent }}</td>
                            <td class="px-4 py-3">
                                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                                    <div class="h-full {{ $progressColor }} text-white text-xs font-semibold text-center"
                                         style="width: {{ $attendancePercent }}%">
                                        {{ $attendancePercent }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
