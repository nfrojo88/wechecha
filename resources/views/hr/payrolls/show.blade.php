@extends('layouts.app')
@section('title', 'Payslip Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Payslip: {{ date('F', mktime(0, 0, 0, $payroll->month, 10)) }} {{ $payroll->year }}</h1>
        <div>
            <a href="{{ route('payrolls.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
            </a>
            <button class="btn btn-sm btn-primary shadow-sm" onclick="window.print()"><i class="fas fa-print"></i> Print Payslip</button>
        </div>
    </div>

    @php
        $totalEarnings = $payroll->basic_salary + $payroll->allowances + $payroll->overtime_pay;
        $totalDeductions = $payroll->deductions + $payroll->tax;
        $netPay = $totalEarnings - $totalDeductions;
    @endphp

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-invoice-dollar mr-2"></i> Employee Payslip</h6>
                    @if($payroll->status == 'pending')
                        <span class="badge badge-warning px-3 py-2">PENDING</span>
                    @else
                        <span class="badge badge-success px-3 py-2"><i class="fas fa-check-circle"></i> PAID on {{ \Carbon\Carbon::parse($payroll->paid_at)->format('M d, Y') }}</span>
                    @endif
                </div>
                <div class="card-body px-5 py-4">
                    <!-- Header -->
                    <div class="row border-bottom pb-4 mb-4">
                        <div class="col-sm-6">
                            <h4 class="font-weight-bold text-gray-900 mb-1">Construct-Pro ERP</h4>
                            <p class="text-muted small mb-0">Monthly Payroll Statement</p>
                        </div>
                        <div class="col-sm-6 text-right">
                            <h6 class="font-weight-bold text-primary mb-1">Period: {{ date('F', mktime(0, 0, 0, $payroll->month, 10)) }} {{ $payroll->year }}</h6>
                            <p class="text-muted small mb-0">Date Generated: {{ $payroll->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <!-- Employee Details -->
                    <div class="row mb-4">
                        <div class="col-sm-12">
                            <table class="table table-sm table-borderless bg-light rounded p-3">
                                <tr>
                                    <th width="20%">Employee ID:</th>
                                    <td width="30%"><strong>{{ $payroll->employee->employee_id }}</strong></td>
                                    <th width="20%">Department:</th>
                                    <td width="30%">{{ $payroll->employee->department->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td><strong>{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</strong></td>
                                    <th>Position:</th>
                                    <td>{{ $payroll->employee->position ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Financials -->
                    <div class="row">
                        <!-- Earnings -->
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-success border-bottom pb-2">Earnings</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td>Basic Salary</td>
                                    <td class="text-right">${{ number_format($payroll->basic_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Allowances</td>
                                    <td class="text-right">${{ number_format($payroll->allowances, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Overtime Pay</td>
                                    <td class="text-right">${{ number_format($payroll->overtime_pay, 2) }}</td>
                                </tr>
                                <tr class="font-weight-bold border-top">
                                    <td>Total Earnings</td>
                                    <td class="text-right text-success">${{ number_format($totalEarnings, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <!-- Deductions -->
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-danger border-bottom pb-2">Deductions</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td>Other Deductions</td>
                                    <td class="text-right">${{ number_format($payroll->deductions, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Tax</td>
                                    <td class="text-right">${{ number_format($payroll->tax, 2) }}</td>
                                </tr>
                                <tr class="font-weight-bold border-top">
                                    <td>Total Deductions</td>
                                    <td class="text-right text-danger">${{ number_format($totalDeductions, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Net Pay -->
                    <div class="row mt-4">
                        <div class="col-12 text-right">
                            <div class="p-3 bg-light rounded d-inline-block border-left-primary">
                                <span class="text-uppercase text-muted font-weight-bold mr-3">Net Pay:</span>
                                <h3 class="mb-0 font-weight-bold text-primary d-inline-block">${{ number_format($netPay, 2) }}</h3>
                            </div>
                        </div>
                    </div>

                    @if($payroll->notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <p class="text-muted small"><strong>Remarks:</strong> {{ $payroll->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                @if($payroll->status == 'pending')
                <div class="card-footer text-center">
                    <form action="{{ route('payrolls.markPaid', $payroll) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-lg px-5 shadow"><i class="fas fa-check-double"></i> Mark as Paid</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .navbar, .sidebar, .btn, footer { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        body { background-color: #fff !important; }
    }
</style>
@endsection
