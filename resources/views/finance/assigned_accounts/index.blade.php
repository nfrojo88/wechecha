@extends('layouts.app')
@section('title', 'My Assigned Accounts')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold"><i class="fas fa-briefcase me-2 text-primary"></i>My Assigned Accounts</h1>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="row">
        @forelse($accounts as $account)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100 border-0 rounded-4" style="transition: transform 0.2s;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-secondary mb-2">{{ $account->code }}</span>
                            <h5 class="card-title fw-bold text-dark">{{ $account->name }}</h5>
                        </div>
                        <div class="p-2 bg-light rounded text-primary">
                            <i class="fas fa-wallet fa-lg"></i>
                        </div>
                    </div>
                    
                    <p class="text-muted small mb-4">{{ Str::limit($account->description, 60) }}</p>
                    
                    <div class="d-flex justify-content-between align-items-end">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Current Balance</span>
                            <h4 class="mb-0 {{ $account->current_balance < 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                ETB {{ number_format($account->current_balance, 2) }}
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 pt-0 pb-4 px-4 text-end">
                    <a href="{{ route('assigned-accounts.show', $account->id) }}" class="btn btn-primary rounded-pill px-4 shadow-sm">Manage <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 p-5 text-center">
                <div class="text-muted mb-3"><i class="fas fa-inbox fa-3x"></i></div>
                <h5 class="fw-bold text-dark">No Accounts Assigned</h5>
                <p class="text-muted">You have not been assigned any chart of accounts to manage.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
