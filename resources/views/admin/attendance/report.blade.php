@extends('layouts.admin')

@section('title', 'Daily Attendance Report')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">📅 Daily Attendance Report</h2>
            <form method="GET" class="flex items-center space-x-2 mt-4 md:mt-0">
                <input type="date" name="date" class="border border-gray-300 rounded px-3 py-1.5 shadow-sm text-sm focus:outline-none focus:ring focus:border-blue-500"
                       value="{{ $date }}" max="{{ now()->format('Y-m-d') }}">
                <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700 shadow">
                    Generate
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-green-100 text-green-900 p-4 rounded shadow text-center">
                <p class="text-sm">✅ Present</p>
                <p class="text-2xl font-bold">{{ $attendances->get('present', collect())->count() }}</p>
            </div>
            <div class="bg-yellow-100 text-yellow-900 p-4 rounded shadow text-center">
                <p class="text-sm">⏰ Late</p>
                <p class="text-2xl font-bold">{{ $attendances->get('late', collect())->count() }}</p>
            </div>
            <div class="bg-blue-100 text-blue-900 p-4 rounded shadow text-center">
                <p class="text-sm">🛌 On Leave</p>
                <p class="text-2xl font-bold">{{ $attendances->get('on_leave', collect())->count() }}</p>
            </div>
            <div class="bg-red-100 text-red-900 p-4 rounded shadow text-center">
                <p class="text-sm">❌ Absent</p>
                <p class="text-2xl font-bold">{{ $allEmployees - $attendances->flatten()->count() }}</p>
            </div>
        </div>

        <div class="text-lg font-semibold mb-4">
            Report for <span class="text-blue-700">{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach(['present', 'late', 'on_leave', 'half_day'] as $status)
                @if($attendances->has($status))
                    <div class="bg-white rounded shadow p-4">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4 capitalize">
                            {{ str_replace('_', ' ', $status) }} Employees
                        </h3>
                        <ul class="divide-y divide-gray-200">
                            @foreach($attendances[$status] as $attendance)
                                <li class="flex justify-between py-2 text-sm">
                                    <span class="text-gray-800">{{ $attendance->employee->user->name }}</span>
                                    <span class="text-gray-500 text-right">
                                    @if($attendance->check_in)
                                            {{ $attendance->check_in }}
                                            @if($attendance->check_out)
                                                - {{ $attendance->check_out }}
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection
