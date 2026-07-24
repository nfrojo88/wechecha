@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="h3 mb-0">
                <i class="fas fa-file-contract me-2"></i>Employee Contract Management
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('contracts.create') }}" class="btn btn-primary btn-sm me-2">
                <i class="fas fa-plus me-1"></i>New Contract
            </a>
            <a href="{{ route('contracts.expiring') }}" class="btn btn-warning btn-sm me-2">
                <i class="fas fa-exclamation-triangle me-1"></i>Expiring
            </a>
            <a href="{{ route('contracts.export') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-download me-1"></i>Export
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Active Contracts</h6>
                    <h3 class="text-success mb-0">{{ $stats['active'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Expiring Soon</h6>
                    <h3 class="text-warning mb-0">{{ $stats['expiring_soon'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Expired</h6>
                    <h3 class="text-danger mb-0">{{ $stats['expired'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Pending Approval</h6>
                    <h3 class="text-info mb-0">{{ $stats['pending_approval'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Employee</label>
                    <select name="employee_id" class="form-select form-select-sm">
                        <option value="">All Employees</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Contracts Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Contract #</th>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Days Left</th>
                        <th>Salary</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contracts as $contract)
                        <tr>
                            <td>
                                <strong>{{ $contract->employee->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $contract->employee->code }}</small>
                            </td>
                            <td>
                                {{ $contract->contract_number }}
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $contract->contract_type }}</span>
                            </td>
                            <td>
                                {{ $contract->start_date->format('M d, Y') }}
                            </td>
                            <td>
                                {{ $contract->end_date->format('M d, Y') }}
                            </td>
                            <td>
                                @if ($contract->isExpired())
                                    <span class="badge bg-danger">Expired</span>
                                @else
                                    @if ($contract->days_remaining <= 30)
                                        <span class="badge bg-warning">{{ $contract->days_remaining }} days</span>
                                    @else
                                        {{ $contract->days_remaining }} days
                                    @endif
                                @endif
                            </td>
                            <td>
                                {{ number_format($contract->salary ?? 0, 2) }}
                            </td>
                            <td>
                                @if ($contract->status === 'draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif ($contract->status === 'pending_approval')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif ($contract->status === 'approved' || $contract->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif ($contract->status === 'expired')
                                    <span class="badge bg-danger">Expired</span>
                                @else
                                    <span class="badge bg-info">{{ ucfirst($contract->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('contracts.show', $contract->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>No contracts found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $contracts->links() }}
    </div>
</div>
@endsection
