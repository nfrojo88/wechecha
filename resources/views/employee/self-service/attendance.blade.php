@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">My Attendance</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Attendance Records</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Day</th>
                                    <th>Status</th>
                                    <th>Check-In Time</th>
                                    <th>Check-Out Time</th>
                                    <th>Duration</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendance as $record)
                                <tr>
                                    <td>{{ $record->attendance_date->format('M d, Y') }}</td>
                                    <td>{{ $record->attendance_date->format('l') }}</td>
                                    <td>
                                        @if ($record->status === 'present')
                                            <span class="badge badge-success">Present</span>
                                        @elseif ($record->status === 'absent')
                                            <span class="badge badge-danger">Absent</span>
                                        @elseif ($record->status === 'leave')
                                            <span class="badge badge-warning">Leave</span>
                                        @elseif ($record->status === 'half-day')
                                            <span class="badge badge-info">Half Day</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($record->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $record->check_in_time ? $record->check_in_time->format('H:i') : '-' }}</td>
                                    <td>{{ $record->check_out_time ? $record->check_out_time->format('H:i') : '-' }}</td>
                                    <td>
                                        @if ($record->check_in_time && $record->check_out_time)
                                            {{ $record->check_in_time->diff($record->check_out_time)->format('%h:%I') }} hrs
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $record->notes ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No attendance records found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($attendance->hasPages())
                    <div class="mt-4">
                        {{ $attendance->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h6 class="text-muted">Total Present Days</h6>
                            <h3 class="text-primary">{{ $attendance->where('status', 'present')->count() }}</h3>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Total Absent Days</h6>
                            <h3 class="text-danger">{{ $attendance->where('status', 'absent')->count() }}</h3>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Total Leave Days</h6>
                            <h3 class="text-warning">{{ $attendance->where('status', 'leave')->count() }}</h3>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Total Half Days</h6>
                            <h3 class="text-info">{{ $attendance->where('status', 'half-day')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
