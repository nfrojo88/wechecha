@extends('layouts.app')
@section('title', 'Generate Payroll')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Generate Payroll Entry</h1>
        <a href="{{ route('payrolls.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payroll Calculation</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('payrolls.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="font-weight-bold">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-control" required>
                                    <option value="">Select Employee...</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">[{{ $employee->employee_id }}] {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->department->name ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="font-weight-bold">Payroll Month <span class="text-danger">*</span></label>
                                <select name="month" class="form-control" required>
                                    @for($m=1; $m<=12; ++$m)
                                        <option value="{{ $m }}" {{ date('m') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold">Payroll Year <span class="text-danger">*</span></label>
                                <input type="number" name="year" class="form-control" value="{{ date('Y') }}" required>
                            </div>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold text-success mb-3">Earnings (+)</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Basic Salary <span class="text-danger">*</span></label>
                                <input type="number" name="basic_salary" class="form-control amount-input" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-4">
                                <label>Allowances</label>
                                <input type="number" name="allowances" class="form-control amount-input" step="0.01" min="0" value="0">
                            </div>
                            <div class="col-md-4">
                                <label>Overtime Pay</label>
                                <input type="number" name="overtime_pay" class="form-control amount-input" step="0.01" min="0" value="0">
                            </div>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold text-danger mb-3">Deductions (-)</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Deductions (Loans, Absences)</label>
                                <input type="number" name="deductions" class="form-control amount-input text-danger" step="0.01" min="0" value="0">
                            </div>
                            <div class="col-md-6">
                                <label>Tax</label>
                                <input type="number" name="tax" class="form-control amount-input text-danger" step="0.01" min="0" value="0">
                            </div>
                        </div>
                        
                        <div class="alert alert-info border-left-info mt-4">
                            <strong>Estimated Net Pay: </strong>$<span id="netPayDisplay">0.00</span>
                        </div>

                        <div class="form-group">
                            <label>Notes / Remarks</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-file-invoice-dollar"></i> Generate Payroll</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.amount-input');
        const netDisplay = document.getElementById('netPayDisplay');

        function calculateNet() {
            const basic = parseFloat(document.querySelector('[name="basic_salary"]').value) || 0;
            const allow = parseFloat(document.querySelector('[name="allowances"]').value) || 0;
            const over  = parseFloat(document.querySelector('[name="overtime_pay"]').value) || 0;
            const ded   = parseFloat(document.querySelector('[name="deductions"]').value) || 0;
            const tax   = parseFloat(document.querySelector('[name="tax"]').value) || 0;

            const net = basic + allow + over - ded - tax;
            netDisplay.textContent = net.toFixed(2);
        }

        inputs.forEach(input => {
            input.addEventListener('input', calculateNet);
        });
    });
</script>
@endsection
