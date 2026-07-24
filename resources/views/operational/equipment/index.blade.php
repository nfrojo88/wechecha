@extends('layouts.app')
@section('title', 'Equipment Master')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-tractor me-2"></i>Equipment Master</h1>
        <a href="{{ route('equipment.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Equipment</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>Code</th><th>Name</th><th>Category</th><th>Hourly Rate</th><th>Daily Rate</th><th>Status</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($equipment as $eq)
                    <tr>
                        <td><strong>{{ $eq->code }}</strong></td>
                        <td>{{ $eq->name }}</td>
                        <td>{{ $eq->category }}</td>
                        <td>{{ number_format($eq->hourly_rate, 2) }}</td>
                        <td>{{ number_format($eq->daily_rate, 2) }}</td>
                        <td><span class="badge bg-{{ $eq->is_active ? 'success' : 'secondary' }}">{{ $eq->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('equipment.show', $eq) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i> Log Productivity</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4">No equipment found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
