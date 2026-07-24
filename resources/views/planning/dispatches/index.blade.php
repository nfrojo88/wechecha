@extends('layouts.app')
@section('title', 'Weekly Dispatches')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-truck-fast me-2"></i>Weekly Plan Dispatches</h1>
        <a href="{{ route('dispatches.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Dispatch</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>Dispatch No</th><th>Project</th><th>Week Range</th><th>Dispatched To</th><th>Status</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($dispatches as $dispatch)
                    <tr>
                        <td><strong>{{ $dispatch->dispatch_no }}</strong></td>
                        <td>{{ $dispatch->project->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($dispatch->week_start)->format('d M') }} - {{ \Carbon\Carbon::parse($dispatch->week_end)->format('d M, Y') }}</td>
                        <td>{{ $dispatch->dispatchedTo->name ?? 'Unknown' }}</td>
                        <td><span class="badge bg-{{ $dispatch->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($dispatch->status) }}</span></td>
                        <td class="text-center"><a href="{{ route('dispatches.show', $dispatch) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4">No weekly plan dispatches found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
