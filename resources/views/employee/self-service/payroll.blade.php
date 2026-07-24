@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">My Payroll</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-success">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-2">YTD Total Salary</h6>
                    <h3 class="text-success">{{ number_format($ytdTotal, 2) }}</h3>
                    <small class="text-muted">Year to Date</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-primary">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-2">Latest Payment</h6>
                    @if ($payrolls->first())
                        <h3 class="text-primary">{{ number_format($payrolls->first()->net_salary, 2) }}</h3>
                        <small class="text-muted">{{ $payrolls->first()->period }}</small>
                    @else
                        <p class="text-muted">No payroll records</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-info">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-2">Total Records</h6>
                    <h3 class="text-info">{{ $payrolls->total() }}</h3>
                    <small class="text-muted">Payroll slips</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Payroll Records</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Period</th>
                                    <th>Gross Salary</th>
                                    <th>Total Deductions</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payrolls as $payroll)
                                <tr>
                                    <td>
                                        <strong>{{ $payroll->period }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $payroll->month }}/{{ $payroll->year }}</small>
                                    </td>
                                    <td>{{ number_format($payroll->gross_salary, 2) }}</td>
                                    <td>{{ number_format($payroll->total_deductions, 2) }}</td>
                                    <td>
                                        <strong class="text-success">{{ number_format($payroll->net_salary, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if ($payroll->status === 'processed')
                                            <span class="badge badge-success">Processed</span>
                                        @elseif ($payroll->status === 'paid')
                                            <span class="badge badge-info">Paid</span>
                                        @elseif ($payroll->status === 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($payroll->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('employee.payroll.download', $payroll) }}" class="btn btn-sm btn-outline-primary" title="Download Slip">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No payroll records found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($payrolls->hasPages())
                    <div class="mt-4">
                        {{ $payrolls->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Salary Structure -->
    @if ($employee->salaryStructure)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Current Salary Structure</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Earnings</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td>Basic Salary</td>
                                    <td class="text-right"><strong>{{ number_format($employee->salaryStructure->basic_salary, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Dearness Allowance</td>
                                    <td class="text-right">{{ number_format($employee->salaryStructure->dearness_allowance, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>House Rent Allowance</td>
                                    <td class="text-right">{{ number_format($employee->salaryStructure->house_rent_allowance, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Conveyance Allowance</td>
                                    <td class="text-right">{{ number_format($employee->salaryStructure->conveyance_allowance, 2) }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Total Earnings</strong></td>
                                    <td class="text-right"><strong class="text-success">{{ number_format($employee->salaryStructure->total_earnings, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Deductions</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td>Professional Tax</td>
                                    <td class="text-right">{{ number_format($employee->salaryStructure->professional_tax, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Provident Fund</td>
                                    <td class="text-right">{{ number_format($employee->salaryStructure->provident_fund, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Income Tax</td>
                                    <td class="text-right">{{ number_format($employee->salaryStructure->income_tax, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Other Deductions</td>
                                    <td class="text-right">{{ number_format($employee->salaryStructure->other_deductions, 2) }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Total Deductions</strong></td>
                                    <td class="text-right"><strong class="text-danger">{{ number_format($employee->salaryStructure->total_deductions, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Net Salary</strong></td>
                                    <td class="text-right"><strong class="text-primary">{{ number_format($employee->salaryStructure->net_salary, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
