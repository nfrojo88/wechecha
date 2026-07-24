@extends('layouts.app')

@section('title', 'Employee Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="fas fa-users me-2 text-primary"></i>Employee Management</h1>
        <p class="text-muted mt-1">Manage all employees across projects</p>
    </div>
    @can('hr.manage')
    <a href="{{ route('employees.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-user-plus me-1"></i> Add New Employee
    </a>
    @endcan
</div>

<!-- Search & Filter Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('employees.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by name, code, or email..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status')=='active')>Active</option>
                    <option value="suspended" @selected(request('status')=='suspended')>Suspended</option>
                    <option value="terminated" @selected(request('status')=='terminated')>Terminated</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="department" class="form-select">
                    <option value="">All Departments</option>
                    @foreach(['Civil', 'Mechanical', 'Electrical', 'Plumbing', 'Admin', 'Finance', 'HR', 'IT'] as $dept)
                    <option value="{{ $dept }}" @selected(request('department')==$dept)>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'department']))
                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Role / Department</th>
                        <th>Project Site</th>
                        <th>Type</th>
                        <th>Basic Salary</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr>
                        <td class="font-monospace small text-muted">{{ $emp->employee_code }}</td>
                        <td class="fw-semibold">{{ $emp->full_name }}</td>
                        <td>
                            <div>{{ $emp->role_title ?? '—' }}</div>
                            <small class="text-muted">{{ $emp->department ?? '' }}</small>
                        </td>
                        <td>
                            @if($emp->project)
                            <a href="{{ route('projects.show', $emp->project) }}" class="text-decoration-none small">
                                {{ $emp->project->name }}
                            </a>
                            @else
                            <span class="text-muted small">HQ / Unassigned</span>
                            @endif
                        </td>
                        <td>
                            @php $typeColor = match($emp->employment_type){
                                'permanent' => 'success',
                                'contract'  => 'warning',
                                'daily'     => 'secondary',
                                default     => 'secondary'
                            }; @endphp
                            <span class="badge bg-{{ $typeColor }}">{{ ucfirst($emp->employment_type) }}</span>
                        </td>
                        <td class="fw-semibold">{{ number_format($emp->basic_salary, 2) }} ETB</td>
                        <td>
                            @php $statColor = match($emp->status){
                                'active'     => 'success',
                                'suspended'  => 'warning',
                                'terminated' => 'danger',
                                default      => 'secondary'
                            }; @endphp
                            <span class="badge bg-{{ $statColor }}">{{ ucfirst($emp->status) }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group" role="group">
                                <a href="{{ route('employees.show', $emp) }}" class="btn btn-sm btn-outline-primary">View</a>
                                @can('hr.manage')
                                <a href="{{ route('employees.edit', $emp) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form action="{{ route('employees.destroy', $emp) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-users fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">No employees found. Add the first one!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($employees->hasPages())
    <div class="card-footer bg-white border-top py-3 d-flex justify-content-end">
        {{ $employees->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
