@extends('layouts.app')
@section('title', 'Manage Account')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold"><i class="fas fa-wallet me-2 text-primary"></i>{{ $account->name }}</h1>
            <span class="badge bg-secondary">Code: {{ $account->code }}</span>
        </div>
        <div>
            <a href="{{ route('assigned-accounts.index') }}" class="btn btn-light border shadow-sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Balance Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-4 bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="text-uppercase fw-bold text-white-50 mb-3">Current Balance</h6>
                    <h2 class="fw-bold mb-0">ETB {{ number_format($account->current_balance, 2) }}</h2>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold text-dark"><i class="fas fa-money-bill-wave me-2 text-success"></i>New Transaction</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('assigned-accounts.pay', $account->id) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Type</label>
                                <select name="type" class="form-select bg-light" required>
                                    <option value="payment">Payment (Out)</option>
                                    <option value="receipt">Receipt (In)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Amount (ETB)</label>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control bg-light" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Reference</label>
                                <input type="text" name="reference" class="form-control bg-light" placeholder="Optional">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-bold text-uppercase">Target Account</label>
                                <select name="target_account_id" class="form-select bg-light" required>
                                    <option value="">Select account...</option>
                                    @foreach($targetAccounts as $target)
                                        <option value="{{ $target->id }}">{{ $target->code }} - {{ $target->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-bold text-uppercase">Description</label>
                                <textarea name="description" class="form-control bg-light" rows="2" required></textarea>
                            </div>
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-success px-4"><i class="fas fa-check me-2"></i> Submit Transaction</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- History -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-bottom-0 pt-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-history me-2 text-info"></i>Transaction History</h5>
            
            <form method="GET" action="{{ route('assigned-accounts.show', $account->id) }}" class="d-flex gap-2">
                <input type="date" name="start_date" class="form-control form-control-sm bg-light" value="{{ $startDate }}">
                <input type="date" name="end_date" class="form-control form-control-sm bg-light" value="{{ $endDate }}">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            </form>
        </div>
        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="py-3">Reference</th>
                            <th class="py-3">Description</th>
                            <th class="py-3">Created By</th>
                            <th class="py-3 text-end">Debit (ETB)</th>
                            <th class="px-4 py-3 text-end">Credit (ETB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            <tr>
                                <td class="px-4"><span class="badge bg-secondary">{{ Carbon\Carbon::parse($entry->entry_date)->format('M d, Y') }}</span></td>
                                <td><span class="text-primary fw-semibold">{{ $entry->reference }}</span></td>
                                <td>{{ $entry->description }}</td>
                                <td>{{ $entry->journalEntry->creator->name ?? 'System' }}</td>
                                <td class="text-end text-success fw-bold">{{ $entry->side === 'debit' ? number_format($entry->amount, 2) : '-' }}</td>
                                <td class="px-4 text-end text-danger fw-bold">{{ $entry->side === 'credit' ? number_format($entry->amount, 2) : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted">No transactions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($entries->hasPages())
        <div class="card-footer bg-white border-top-0 pt-3">
            {{ $entries->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
