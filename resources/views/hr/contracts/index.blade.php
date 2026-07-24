@extends('layouts.app')
@section('title', 'Employee Contracts')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-file-signature me-2"></i>Employee Contracts</h1>
        <a href="{{ route('contracts.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Contract</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>Employee</th><th>Type</th><th>Start Date</th><th>End Date</th><th>Salary</th><th>Status</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($contracts as $c)
                    <tr>
                        <td>{{ $c->employee->first_name }} {{ $c->employee->last_name }}</td>
                        <td>{{ ucfirst($c->contract_type) }}</td>
                        <td>{{ $c->start_date->format('d M Y') }}</td>
                        <td>{{ $c->end_date ? $c->end_date->format('d M Y') : 'N/A' }}</td>
                        <td>{{ number_format($c->salary, 2) }}</td>
                        <td><span class="badge bg-{{ $c->status=='active'?'success':($c->status=='expired'?'warning':'danger') }}">{{ ucfirst($c->status) }}</span></td>
                        <td class="text-center"><a href="{{ route('contracts.show', $c) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4">No contracts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
