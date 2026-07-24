@extends('layouts.app')
@section('title', 'Emergency Funds')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-life-ring me-2"></i>Emergency Funds</h1>
        <a href="{{ route('emergency-funds.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Request</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>ID</th><th>Project</th><th>Requested Amt</th><th>Status</th><th>Requested By</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($funds as $f)
                    <tr>
                        <td>#{{ $f->id }}</td>
                        <td>{{ $f->project->name }}</td>
                        <td>{{ number_format($f->requested_amount, 2) }}</td>
                        <td><span class="badge bg-{{ $f->status=='pending'?'warning':($f->status=='approved'?'success':'secondary') }}">{{ ucfirst($f->status) }}</span></td>
                        <td>{{ $f->requestedBy->name }}</td>
                        <td class="text-center"><a href="{{ route('emergency-funds.show', $f) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4">No emergency fund requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
