@extends('layouts.app')
@section('title', 'Audit Logs')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-clipboard-list me-2"></i>Activity Time Logs</h1>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>User</th><th>Page Visited</th><th>Entered At</th><th>Duration (s)</th><th>IP Address</th></tr>
                </thead>
                <tbody>
                    @forelse($timeLogs as $log)
                    <tr>
                        <td><strong>{{ $log->user->name }}</strong></td>
                        <td><span class="text-truncate d-inline-block" style="max-width: 300px;" title="{{ $log->page_url }}">{{ $log->page_url }}</span></td>
                        <td>{{ $log->entered_at->format('d M Y, h:i A') }}</td>
                        <td>{{ $log->duration_seconds ?? 'Active' }}</td>
                        <td>{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4">No activity time logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
