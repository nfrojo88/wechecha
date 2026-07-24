@extends('layouts.app')
@section('title', 'Payroll Records')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Payroll Records</h1>
        <a href="{{ route('payrolls.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Generate Payroll
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Payroll Entries</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Employee</th>
                        <th>Period</th>
                        <th>Basic Salary</th>
                        <th>Net Pay</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                        @php
                            $monthName = date('F', mktime(0, 0, 0, $payroll->month, 10));
                            $netPay = $payroll->basic_salary + $payroll->allowances + $payroll->overtime_pay - $payroll->deductions - $payroll->tax;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</strong><br>
                                <small class="text-muted">{{ $payroll->employee->employee_id }}</small>
                            </td>
                            <td>{{ $monthName }} {{ $payroll->year }}</td>
                            <td>${{ number_format($payroll->basic_salary, 2) }}</td>
                            <td><strong class="text-success">${{ number_format($netPay, 2) }}</strong></td>
                            <td>
                                @if($payroll->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($payroll->status == 'paid')
                                    <span class="badge badge-success">Paid on {{ \Carbon\Carbon::parse($payroll->paid_at)->format('M d, Y') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('payrolls.show', $payroll) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View Slip
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No payroll records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payrolls->hasPages())
        <div class="card-footer">
            {{ $payrolls->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
