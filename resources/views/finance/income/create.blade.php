@extends('layouts.app')
@section('title', 'Record Income')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-plus-circle me-2"></i>Record New Income</h1>
        <a href="{{ route('income.index') }}" class="btn btn-outline-secondary shadow-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4" style="max-width: 800px;">
        <div class="card-body">
            <form action="{{ route('income.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Income Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="project_payment">Project Payment</option>
                            <option value="advance">Advance</option>
                            <option value="other">Other Income</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                        <input type="date" name="income_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Project (Optional)</label>
                        <select name="project_id" class="form-select">
                            <option value="">-- No Project --</option>
                            @foreach($projects ?? [] as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Amount (ETB) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bank Account</label>
                        <select name="bank_account_id" class="form-select">
                            <option value="">-- Select Bank Account --</option>
                            @foreach($bankAccounts ?? [] as $b)
                                <option value="{{ $b->id }}">{{ $b->bank_name }} ({{ $b->account_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control" required placeholder="Brief description of this income...">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Additional Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Income Record</button>
                    <a href="{{ route('income.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
