@extends('layouts.app')

@section('title', 'Report Damage - ' . $asset->employee->full_name)

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('employees.show', $asset->employee) }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="h3 mb-0">Report Asset Damage</h1>
        <small class="text-muted">{{ $asset->employee->full_name }} • {{ $asset->product->name }}</small>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-danger bg-opacity-10 border-danger">
                <h5 class="mb-0 text-danger"><i class="fa-solid fa-exclamation-triangle me-2"></i>Asset Damage Report</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-4">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    <strong>Important:</strong> This report will mark the asset as damaged and trigger a review process for potential replacement or repair.
                </div>

                <div class="alert alert-light border mb-4">
                    <h6 class="mb-3"><i class="fa-solid fa-info-circle text-info me-2"></i>Asset Information</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Employee</small>
                            <h6 class="mb-0">{{ $asset->employee->full_name }}</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Department</small>
                            <h6 class="mb-0">{{ $asset->employee->department }}</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Asset</small>
                            <h6 class="mb-0">{{ $asset->product->name }}</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Unit Price</small>
                            <h6 class="mb-0">Br {{ number_format($asset->product->unit_cost ?? 0, 2) }}</h6>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Assigned Date</small>
                            <h6 class="mb-0">{{ optional($asset->assigned_date)->format('d M Y') ?? 'N/A' }}</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Duration in Use</small>
                            @php
                                $days = $asset->assigned_date->diffInDays(now());
                            @endphp
                            <h6 class="mb-0">{{ $days }} days</h6>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('employee-assets.damage-store', $asset) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Damage Severity <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="severity" value="minor" id="severity_minor"
                                           @checked(old('severity')=='minor')>
                                    <label class="form-check-label" for="severity_minor">
                                        <strong>Minor</strong>
                                        <br><small class="text-muted">Cosmetic damage</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="severity" value="moderate" id="severity_moderate"
                                           @checked(old('severity')=='moderate')>
                                    <label class="form-check-label" for="severity_moderate">
                                        <strong>Moderate</strong>
                                        <br><small class="text-muted">Functional impact</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="severity" value="severe" id="severity_severe"
                                           @checked(old('severity')=='severe')>
                                    <label class="form-check-label" for="severity_severe">
                                        <strong>Severe</strong>
                                        <br><small class="text-muted">Total loss/unusable</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @error('severity')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cause of Damage <span class="text-danger">*</span></label>
                        <select name="damage_cause" class="form-select @error('damage_cause') is-invalid @enderror" required>
                            <option value="">-- Select Cause --</option>
                            <option value="accidental" @selected(old('damage_cause')=='accidental')>Accidental Damage</option>
                            <option value="misuse" @selected(old('damage_cause')=='misuse')>Misuse/Improper Handling</option>
                            <option value="wear_tear" @selected(old('damage_cause')=='wear_tear')>Normal Wear & Tear</option>
                            <option value="manufacturing" @selected(old('damage_cause')=='manufacturing')>Manufacturing Defect</option>
                            <option value="theft" @selected(old('damage_cause')=='theft')>Theft/Loss</option>
                            <option value="other" @selected(old('damage_cause')=='other')>Other</option>
                        </select>
                        @error('damage_cause')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Detailed Description <span class="text-danger">*</span></label>
                        <textarea name="damage_description" class="form-control @error('damage_description') is-invalid @enderror"
                                  rows="5" placeholder="Provide detailed description of the damage..." required>{{ old('damage_description') }}</textarea>
                        <small class="text-muted d-block mt-1">Include how damage occurred, which parts are affected, and current functionality</small>
                        @error('damage_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reported By <span class="text-danger">*</span></label>
                        <input type="text" name="reported_by" class="form-control @error('reported_by') is-invalid @enderror"
                               value="{{ old('reported_by', auth()->user()->name) }}" placeholder="Your name" required>
                        @error('reported_by')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Employee Acknowledgment</label>
                        <div class="form-check">
                            <input class="form-check-input @error('employee_acknowledged') is-invalid @enderror"
                                   type="checkbox" name="employee_acknowledged" id="acknowledge" value="1"
                                   @checked(old('employee_acknowledged')=='1')>
                            <label class="form-check-label" for="acknowledge">
                                Employee acknowledges and accepts responsibility for the damage
                            </label>
                            @error('employee_acknowledged')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger flex-grow-1">
                            <i class="fa-solid fa-check me-2"></i>Submit Damage Report
                        </button>
                        <a href="{{ route('employees.show', $asset->employee) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fa-solid fa-shield me-2"></i>Damage Categories</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block mb-1"><strong>Minor Damage</strong></small>
                    <small class="text-muted">Scratches, dents, cosmetic issues. No functional impact.</small>
                </div>
                <hr>
                <div class="mb-3">
                    <small class="text-muted d-block mb-1"><strong>Moderate Damage</strong></small>
                    <small class="text-muted">Reduced functionality, broken components, but partially usable.</small>
                </div>
                <hr>
                <div>
                    <small class="text-muted d-block mb-1"><strong>Severe Damage</strong></small>
                    <small class="text-muted">Complete loss of functionality, unusable, or total loss.</small>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fa-solid fa-bookmark me-2"></i>Next Steps</h6>
            </div>
            <div class="card-body">
                <ol class="small mb-0">
                    <li>Report submitted for review</li>
                    <li>Manager evaluates damage</li>
                    <li>Determine replacement/repair/write-off</li>
                    <li>Process insurance claim if applicable</li>
                    <li>Update asset status</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@endsection
