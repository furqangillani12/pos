@extends('layouts.admin')

@section('title', 'Attendance Records')

@section('content')
    <div class="space-y-6">

        <!-- Header Buttons -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <h2 class="text-xl font-bold text-gray-800">Attendance Records</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.attendance.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium shadow">
                    + Mark Attendance
                </a>
                <a href="{{ route('admin.attendance.report') }}"
                   class="bg-cyan-600 text-white px-4 py-2 rounded hover:bg-cyan-700 text-sm font-medium shadow">
                    📄 Daily Report
                </a>
            </div>
        </div>

        <!-- Filter by Date -->
        <div class="bg-white p-4 rounded shadow">
            <form method="GET" class="flex flex-col md:flex-row md:items-center gap-4">
                <label class="font-medium text-gray-700">
                    Select Date:
                    <input type="date" name="date" class="mt-1 block w-full md:w-auto border border-gray-300 rounded px-3 py-2 text-sm"
                           value="{{ $date }}" max="{{ now()->format('Y-m-d') }}">
                </label>
                <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm font-medium shadow">
                    Filter
                </button>
            </form>
        </div>

        <!-- Attendance Table -->
        <div class="bg-white p-4 rounded shadow overflow-x-auto">
            <table class="min-w-full text-sm text-left border">
                <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="p-3 border">Employee</th>
                    <th class="p-3 border">Date</th>
                    <th class="p-3 border">Status</th>
                    <th class="p-3 border">Check In</th>
                    <th class="p-3 border">Check Out</th>
                    <th class="p-3 border">Notes</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                @forelse($attendances as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">{{ $attendance->employee->user->name }}</td>
                        <td class="p-3">{{ $attendance->date->format('M d, Y') }}</td>
                        <td class="p-3">
                            @php
                                $badgeColors = [
                                    'present' => 'bg-green-100 text-green-700',
                                    'late' => 'bg-yellow-100 text-yellow-800',
                                    'on_leave' => 'bg-red-100 text-red-700',
                                    'half_day' => 'bg-purple-100 text-purple-700',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded {{ $badgeColors[$attendance->status] ?? 'bg-gray-200 text-gray-700' }}">
                                {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                            </span>
                        </td>
                        <td class="p-3">{{ $attendance->check_in }}</td>
                        <td class="p-3">
                            @if($attendance->check_out)
                                {{ $attendance->check_out }}
                            @elseif($attendance->status !== 'on_leave')
                                <form method="POST" action="{{ route('admin.attendance.checkout', $attendance) }}">
                                    @csrf
                                    <button type="submit"
                                            class="bg-gray-100 hover:bg-gray-200 text-blue-600 text-xs px-3 py-1 rounded shadow-sm">
                                        Mark Checkout
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-xs">N/A</span>
                            @endif
                        </td>

                        <td class="p-3">
                            @if($attendance->notes)
                                <details class="text-xs text-gray-700">
                                    <summary class="cursor-pointer text-blue-600">View</summary>
                                    <div class="mt-1">{{ $attendance->notes }}</div>
                                </details>
                            @else
                                <span class="text-gray-400">No notes</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 p-4">No attendance records found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $attendances->appends(['date' => $date])->links() }}
            </div>
        </div>

    </div>
@endsection
