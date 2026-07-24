@extends('layouts.app')

@section('title', 'Edit Slip Sequence')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h4><i class="fas fa-edit me-2"></i>Edit Slip Sequence</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('store-manager.slip-sequences.update', $slipSequence) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Store</label>
                                <div class="form-control-plaintext">{{ $slipSequence->store->name }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Type</label>
                                <div class="form-control-plaintext">
                                    @if($slipSequence->slip_type === 'receive')
                                    <span class="badge bg-success"><i class="fas fa-arrow-down me-1"></i>GRN</span>
                                    @else
                                    <span class="badge bg-info"><i class="fas fa-arrow-up me-1"></i>SIN</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Label *</label>
                                <input type="text" name="label" class="form-control" 
                                       value="{{ $slipSequence->label }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prefix</label>
                                <input type="text" name="prefix" class="form-control" 
                                       value="{{ $slipSequence->prefix }}" maxlength="50">
                            </div>
                        </div>

                        <hr>
                        <h5>Book Information (Read-Only)</h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Book Range</label>
                                <div class="form-control-plaintext">
                                    {{ $slipSequence->book_start_no }} - {{ $slipSequence->book_end_no }}
                                    <br><small class="text-muted">Total: {{ $slipSequence->book_end_no - $slipSequence->book_start_no + 1 }} slips</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Next Available Slip</label>
                                <div class="form-control-plaintext">
                                    <div class="badge bg-primary fs-6">{{ $slipSequence->getNextSlipNumber() }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Usage Progress</label>
                                <div class="form-control-plaintext">
                                    <strong>{{ $slipSequence->used_count }}</strong> / {{ $slipSequence->book_end_no - $slipSequence->book_start_no + 1 }} used
                                    <div class="progress mt-2" style="height: 8px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $slipSequence->getPercentageUsed() }}%;"
                                             aria-valuenow="{{ $slipSequence->getPercentageUsed() }}" 
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted">{{ $slipSequence->getPercentageUsed() }}% - {{ $slipSequence->getRemainingSlips() }} remaining</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-control-plaintext">
                                    @if($slipSequence->status === 'active')
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Active</span>
                                    @elseif($slipSequence->status === 'full')
                                    <span class="badge bg-danger"><i class="fas fa-exclamation me-1"></i>Full</span>
                                    @else
                                    <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3">{{ $slipSequence->notes }}</textarea>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <div>
                                @if($slipSequence->status === 'active')
                                <form action="{{ route('store-manager.slip-sequences.deactivate', $slipSequence) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-pause me-1"></i>Deactivate
                                    </button>
                                </form>
                                @elseif($slipSequence->status !== 'full')
                                <form action="{{ route('store-manager.slip-sequences.reactivate', $slipSequence) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-play me-1"></i>Reactivate
                                    </button>
                                </form>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('store-manager.slip-sequences.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Sequence Status</h6>
                </div>
                <div class="card-body small">
                    <div class="mb-3">
                        <strong>Current Status:</strong>
                        @if($slipSequence->status === 'active')
                        <span class="badge bg-success">Active - Slips will be assigned from this sequence</span>
                        @elseif($slipSequence->status === 'full')
                        <span class="badge bg-danger">Full - All slips in this book have been used</span>
                        @else
                        <span class="badge bg-secondary">Inactive - Slips will not be assigned from this sequence</span>
                        @endif
                    </div>

                    <hr>
                    <strong>Next Slip to Assign:</strong>
                    <div class="fs-5 fw-bold text-primary">{{ $slipSequence->getNextSlipNumber() }}</div>

                    <hr>
                    <strong>Remaining Slips:</strong>
                    <div class="fs-5">{{ $slipSequence->getRemainingSlips() }}</div>

                    @if($slipSequence->getRemainingSlips() < 10)
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Low on slips! Only {{ $slipSequence->getRemainingSlips() }} left in this book.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
