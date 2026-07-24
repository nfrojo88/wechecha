@extends('layouts.app')
@section('title', 'Subcontractors')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-handshake me-2"></i>Subcontractor Agreements</h1>
        <a href="{{ route('subcontractors.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Agreement</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>Agreement No</th><th>Subcontractor</th><th>Project</th><th>Contract Value</th><th>Status</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($agreements as $agreement)
                    <tr>
                        <td><strong>{{ $agreement->agreement_no }}</strong></td>
                        <td>{{ $agreement->subcontractor_name }}</td>
                        <td>{{ $agreement->project->name }}</td>
                        <td>{{ number_format($agreement->contract_value, 2) }}</td>
                        <td><span class="badge bg-{{ $agreement->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($agreement->status) }}</span></td>
                        <td class="text-center"><a href="{{ route('subcontractors.show', $agreement) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4">No subcontractor agreements found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
