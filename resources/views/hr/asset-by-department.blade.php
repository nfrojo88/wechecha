@extends('layouts.app')

@section('title', 'Assets - ' . $department . ' Department')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0">{{ $department }} Department</h1>
        <small class="text-muted">Assets assigned to employees</small>
        <br>
        <a href="{{ route('assets.dashboard') }}" class="btn btn-sm btn-outline-secondary mt-2">
            <i class="fa-solid fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>
    <div>
        <span class="badge bg-primary" style="font-size: 1rem;">{{ $assets->total() }} Assets</span>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Role/Designation</th>
                        <th>Asset Name</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Assigned</th>
                        <th>Status</th>
                        <th>Unit Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td>
                            <a href="{{ route('employees.show', $asset->employee) }}" class="text-decoration-none">
                                <strong>{{ $asset->employee->full_name }}</strong>
                            </a>
                            <br>
                            <small class="text-muted">{{ $asset->employee->employee_code }}</small>
                        </td>
                        <td>{{ $asset->employee->role_title ?? 'N/A' }}</td>
                        <td>{{ $asset->product->name }}</td>
                        <td>{{ $asset->product->type ?? 'General' }}</td>
                        <td>{{ $asset->product->category ?? 'N/A' }}</td>
                        <td>{{ $asset->assigned_date->format('d M Y') }}</td>
                        <td>
                            @if($asset->status === 'assigned')
                                <span class="badge bg-primary">Assigned</span>
                            @elseif($asset->status === 'in_use')
                                <span class="badge bg-success">In Use</span>
                            @elseif($asset->status === 'returned')
                                <span class="badge bg-warning">Returned</span>
                            @elseif($asset->status === 'damaged')
                                <span class="badge bg-danger">Damaged</span>
                            @endif
                        </td>
                        <td>Br {{ number_format($asset->product->unit_cost ?? 0, 2) }}</td>
                        <td>
                            <a href="{{ route('employees.show', $asset->employee) }}" class="btn btn-sm btn-outline-secondary" title="View Employee">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fa-solid fa-inbox fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">No assets in this department</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($assets->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $assets->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

@endsection
