@extends('layouts.app')

@section('title', 'Pending Asset Returns')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pending Asset Returns</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Assets Awaiting Approval</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Asset Name</th>
                            <th>SKU</th>
                            <th>Assigned Date</th>
                            <th>Return Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingReturns as $return)
                            <tr>
                                <td>{{ $return->employee->full_name ?? 'N/A' }} <br><small class="text-muted">{{ $return->employee->employee_code ?? '' }}</small></td>
                                <td>{{ $return->product->name ?? 'N/A' }}</td>
                                <td>{{ $return->product->sku ?? 'N/A' }}</td>
                                <td>{{ $return->assigned_date ? $return->assigned_date->format('M d, Y') : 'N/A' }}</td>
                                <td><span class="badge bg-warning text-dark">Pending</span></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $return->id }}">
                                        Approve
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $return->id }}">
                                        Reject
                                    </button>

                                    <!-- Approve Modal -->
                                    <div class="modal fade" id="approveModal{{ $return->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form action="{{ route('asset-returns.approve', $return->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Approve Asset Return</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to approve the return of <strong>{{ $return->product->name ?? 'this asset' }}</strong> from <strong>{{ $return->employee->full_name ?? 'this employee' }}</strong>?</p>
                                                        <div class="mb-3">
                                                            <label for="return_notes" class="form-label">Notes (Optional)</label>
                                                            <textarea class="form-control" name="return_notes" rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success">Approve Return</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $return->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form action="{{ route('asset-returns.reject', $return->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject Asset Return</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to reject the return of <strong>{{ $return->product->name ?? 'this asset' }}</strong>?</p>
                                                        <div class="mb-3">
                                                            <label for="return_notes" class="form-label text-danger">Rejection Reason (Required)</label>
                                                            <textarea class="form-control" name="return_notes" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Reject Return</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No pending asset returns found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $pendingReturns->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
