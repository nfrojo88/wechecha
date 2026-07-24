@extends('layouts.app')

@section('content')
<style>
    .stat-card {
        border-radius: 8px;
        background: #fff;
        border: 1px solid #e0e0e0;
        padding: 15px 20px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .stat-card.border-blue { border-left: 4px solid #007bff; }
    .stat-card.border-lightblue { border-left: 4px solid #0dcaf0; }
    .stat-card.border-red { border-left: 4px solid #dc3545; }
    .stat-card.border-yellow { border-left: 4px solid #ffc107; }
    .stat-card.border-gray { border-left: 4px solid #6c757d; }
    .stat-card.border-green { border-left: 4px solid #198754; }

    .stat-title { font-size: 13px; color: #6c757d; font-weight: 600; margin-bottom: 5px; }
    .stat-value { font-size: 22px; font-weight: 700; color: #212529; margin: 0; }
    
    .payroll-table th {
        font-size: 13px;
        font-weight: 600;
        color: #495057;
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        padding: 12px 15px;
    }
    .payroll-table td {
        font-size: 14px;
        padding: 12px 15px;
        vertical-align: middle;
        font-weight: 500;
    }
    
    .text-gross { color: #0d6efd; font-weight: 700; }
    .text-taxable { color: #ffc107; }
    .text-deduction { color: #dc3545; }
    .text-net { color: #198754; font-weight: 700; }
    
    .dept-badge {
        background: #6c757d;
        color: white;
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 12px;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0" style="font-weight: 700; color: #2c3e50;">
            <i class="fas fa-users text-primary me-2"></i>Payroll Management
        </h2>
        <div>
            <span class="badge bg-light text-dark border p-2">
                <i class="fas fa-info-circle me-1"></i> Roster Managed by HR
            </span>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4 g-3">
        <div class="col-md-2">
            <div class="stat-card border-blue h-100">
                <div class="stat-title">Total Employees</div>
                <div class="stat-value">{{ $totals['employees'] }}</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card border-lightblue h-100">
                <div class="stat-title">Total Gross</div>
                <div class="stat-value">ETB {{ number_format($totals['gross'], 2) }}</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card border-red h-100">
                <div class="stat-title">Total Tax</div>
                <div class="stat-value">ETB {{ number_format($totals['tax'], 2) }}</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card border-yellow h-100">
                <div class="stat-title">Emp. Pension (7%)</div>
                <div class="stat-value">ETB {{ number_format($totals['emp_pension'], 2) }}</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card border-gray h-100">
                <div class="stat-title">Comp. Pension (11%)</div>
                <div class="stat-value">ETB {{ number_format($totals['comp_pension'], 2) }}</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card border-green h-100">
                <div class="stat-title">Net Payable</div>
                <div class="stat-value">ETB {{ number_format($totals['net_payable'], 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover payroll-table mb-0">
                <thead>
                    <tr>
                        <th>Emp ID</th>
                        <th>Full Name</th>
                        <th>Department</th>
                        <th>Base Salary</th>
                        <th>Gross Salary</th>
                        <th>Taxable</th>
                        <th>Deductions</th>
                        <th class="text-danger">Pension (7%)</th>
                        <th class="text-danger">Tax Amount</th>
                        <th class="text-success">Net Salary</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payrollData as $row)
                        <tr>
                            <td class="text-muted">{{ $row['emp_id'] }}</td>
                            <td style="font-weight: 700; color: #212529;">{{ $row['name'] }}</td>
                            <td><span class="dept-badge">{{ $row['department'] }}</span></td>
                            <td>ETB {{ number_format($row['base_salary'], 2) }}</td>
                            <td class="text-gross">ETB {{ number_format($row['gross_salary'], 2) }}</td>
                            <td class="text-taxable">ETB {{ number_format($row['taxable'], 2) }}</td>
                            <td class="text-deduction">-ETB {{ number_format($row['deductions'], 2) }}</td>
                            <td class="text-deduction">-ETB {{ number_format($row['pension'], 2) }}</td>
                            <td class="text-deduction">-ETB {{ number_format($row['tax_amount'], 2) }}</td>
                            <td class="text-net">ETB {{ number_format($row['net_salary'], 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('payroll.employee', $row['id']) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="fas fa-users-slash fs-1 mb-3"></i>
                                <h5>No active employees found</h5>
                                <p>Add employees with salary details to see payroll calculations.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
