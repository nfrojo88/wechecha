@extends('layouts.app')

@section('title', 'Pending Role Assignment')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 text-center">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-warning bg-gradient text-dark py-4">
                    <div class="mb-2">
                        <i class="bi bi-clock-history display-3"></i>
                    </div>
                    <h3 class="fw-bold mb-0">Role Assignment Pending</h3>
                </div>
                <div class="card-body p-4 p-md-5">
                    <h5 class="fw-bold text-dark mb-3">Hello, {{ auth()->user()->name }}!</h5>
                    <p class="text-muted fs-6 mb-4">
                        Your system account has been successfully created. However, a system role has not been assigned to your account yet.
                    </p>
                    
                    <div class="alert alert-info border-0 rounded-3 p-3 mb-4 text-start d-flex align-items-center gap-3">
                        <i class="bi bi-info-circle-fill fs-3 text-info"></i>
                        <div>
                            <strong>What to do next?</strong><br>
                            Please contact your <strong>Administrator / HR Manager</strong> to assign your role so you can access your dashboard and system features.
                        </div>
                    </div>

                    @if(auth()->user()->employee)
                    <div class="bg-light p-3 rounded-3 mb-4 text-start">
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted d-block">Employee Code:</small>
                                <span class="fw-bold text-dark">{{ auth()->user()->employee->employee_code }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Registered Phone:</small>
                                <span class="fw-bold text-dark">{{ auth()->user()->employee->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-lg px-4 rounded-pill">
                                <i class="bi bi-box-arrow-right me-2"></i> Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
