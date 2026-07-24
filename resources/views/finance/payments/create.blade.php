@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="fa-solid fa-arrow-left"></i></a>
    <h1 class="h3 mb-0">Record Client Payment</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('payments.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Project <span class="text-danger">*</span></label>
                    <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                        <option value="">— Select Project —</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}" @selected(old('project_id')==$p->id)>{{ $p->name }} ({{ $p->code }})</option>
                        @endforeach
                    </select>
                    @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Reference Number <span class="text-danger">*</span></label>
                    <input type="text" name="reference_number"
                           class="form-control @error('reference_number') is-invalid @enderror"
                           value="{{ old('reference_number', 'PAY-'.date('Ym').'-'.rand(1000,9999)) }}" required>
                    @error('reference_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                    <select name="payment_type" class="form-select @error('payment_type') is-invalid @enderror" required>
                        @foreach($types as $key => $label)
                        <option value="{{ $key }}" @selected(old('payment_type')==$key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('payment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Amount (ETB) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount"
                           class="form-control @error('amount') is-invalid @enderror"
                           value="{{ old('amount') }}" required>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                    <input type="date" name="payment_date"
                           class="form-control @error('payment_date') is-invalid @enderror"
                           value="{{ old('payment_date', date('Y-m-d')) }}" required>
                    @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control"
                           value="{{ old('description') }}" placeholder="e.g. Progress billing for June 2026">
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
