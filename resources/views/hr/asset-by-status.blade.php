@extends('layouts.app')

@section('title', $statusTitle)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0">{{ $statusTitle }}</h1>
        <a href="{{ route('assets.dashboard') }}" class="btn btn-sm btn-outline-secondary mt-2">
            <i class="fa-solid fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>
    <div>
        <span class="badge bg-primary" style="font-size: 1rem;">{{ $assets->total() }} Total</span>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Asset Name</th>
                        <th>Asset Type</th>
                        <th>Assigned Date</th>
                        @if($status === 'returned')
                        <th>Returned Date</th>
                        @endif
                        @if($status === 'damaged')
                        <th>Damage Reported</th>
                        @endif
                        <th>Unit Price</th>
                        <th>Notes</th>
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
                        </td>
                        <td>{{ $asset->employee->department }}</td>
                        <td>{{ $asset->product->name }}</td>
                        <td>{{ $asset->product->type ?? 'General' }}</td>
                        <td>{{ $asset->assigned_date->format('d M Y') }}</td>
                        @if($status === 'returned')
                        <td>{{ $asset->returned_date->format('d M Y') }}</td>
                        @endif
                        @if($status === 'damaged')
                        <td>{{ $asset->updated_at->format('d M Y') }}</td>
                        @endif
                        <td>Br {{ number_format($asset->product->unit_cost ?? 0, 2) }}</td>
                        <td>
                            @if($asset->notes)
                            <small class="text-muted">{{ Str::limit($asset->notes, 30) }}</small>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
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
                            <p class="mb-0">No {{ strtolower($statusTitle) }}</p>
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
