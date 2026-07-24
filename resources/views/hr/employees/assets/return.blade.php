@extends('layouts.app')

@section('title', 'Return Asset - ' . $asset->employee->full_name)

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('employees.show', $asset->employee) }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="h3 mb-0">Return Asset</h1>
        <small class="text-muted">{{ $asset->employee->full_name }} • {{ $asset->product->name }}</small>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="alert alert-light border mb-4">
                    <h6 class="mb-3"><i class="fa-solid fa-info-circle text-info me-2"></i>Asset Details</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Employee</small>
                            <h6 class="mb-0">{{ $asset->employee->full_name }}</h6>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Employee Code</small>
                            <h6 class="mb-0">{{ $asset->employee->employee_code }}</h6>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Asset Name</small>
                            <h6 class="mb-0">{{ $asset->product->name }}</h6>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Asset Type</small>
                            <h6 class="mb-0">{{ $asset->product->type ?? 'General' }}</h6>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Unit Price</small>
                            <h6 class="mb-0">Br {{ number_format($asset->product->unit_cost ?? 0, 2) }}</h6>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Assigned Date</small>
                            <h6 class="mb-0">{{ $asset->assigned_date->format('d M Y') }}</h6>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('employee-assets.return-store', $asset) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Return Date <span class="text-danger">*</span></label>
                        <input type="date" name="returned_date" class="form-control @error('returned_date') is-invalid @enderror"
                               value="{{ old('returned_date', date('Y-m-d')) }}" required>
                        @error('returned_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Condition of Asset <span class="text-danger">*</span></label>
                        <select name="condition" class="form-select @error('condition') is-invalid @enderror" required>
                            <option value="">-- Select Condition --</option>
                            <option value="good" @selected(old('condition')=='good')>Good - No Damage</option>
                            <option value="fair" @selected(old('condition')=='fair')>Fair - Minor Wear</option>
                            <option value="damaged" @selected(old('condition')=='damaged')>Damaged - Needs Repair</option>
                        </select>
                        @error('condition')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Return Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4"
                                  placeholder="e.g., Returned in good condition. No issues found.">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Received By</label>
                        <input type="text" name="received_by" class="form-control @error('received_by') is-invalid @enderror"
                               value="{{ old('received_by', auth()->user()->name) }}" placeholder="Name of person receiving asset">
                        @error('received_by')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success flex-grow-1">
                            <i class="fa-solid fa-check me-2"></i>Confirm Return
                        </button>
                        <a href="{{ route('employees.show', $asset->employee) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fa-solid fa-checklist me-2"></i>Return Checklist</h6>
            </div>
            <div class="card-body">
                <div class="checklist-item mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check1">
                        <label class="form-check-label" for="check1">
                            <strong>Physical Condition</strong>
                            <br><small class="text-muted">Verify asset is in acceptable condition</small>
                        </label>
                    </div>
                </div>
                <div class="checklist-item mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check2">
                        <label class="form-check-label" for="check2">
                            <strong>All Accessories</strong>
                            <br><small class="text-muted">Check all original accessories are included</small>
                        </label>
                    </div>
                </div>
                <div class="checklist-item mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check3">
                        <label class="form-check-label" for="check3">
                            <strong>Serial Number Verified</strong>
                            <br><small class="text-muted">Confirm serial number matches records</small>
                        </label>
                    </div>
                </div>
                <div class="checklist-item mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check4">
                        <label class="form-check-label" for="check4">
                            <strong>Data Wiped (If Applicable)</strong>
                            <br><small class="text-muted">Remove personal data from electronic assets</small>
                        </label>
                    </div>
                </div>
                <div class="checklist-item">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check5">
                        <label class="form-check-label" for="check5">
                            <strong>Employee Signed Off</strong>
                            <br><small class="text-muted">Employee confirmed asset handover</small>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fa-solid fa-question-circle me-2"></i>Need Help?</h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>What information should I provide?</strong>
                </p>
                <ul class="small text-muted mb-0">
                    <li>Date asset is being returned</li>
                    <li>Overall condition (Good/Fair/Damaged)</li>
                    <li>Any notes about wear or damage</li>
                    <li>Who is receiving the asset</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
