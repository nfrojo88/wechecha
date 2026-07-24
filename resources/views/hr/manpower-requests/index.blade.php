@extends('layouts.app')
@section('title', 'Manpower Requests')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-users-cog me-2"></i>Manpower Requests</h1>
        <a href="{{ route('manpower-requests.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Request</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>Project</th><th>Type</th><th>Req. Date</th><th>Status</th><th>Requested By</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($requests as $r)
                    <tr>
                        <td>{{ $r->project->name }}</td>
                        <td>{{ ucfirst(str_replace('_',' ',$r->type)) }}</td>
                        <td>{{ $r->required_date->format('d M Y') }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($r->status) }}</span></td>
                        <td>{{ $r->requestedBy->name }}</td>
                        <td class="text-center"><a href="{{ route('manpower-requests.show', $r) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No manpower requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
