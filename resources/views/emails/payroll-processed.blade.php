@component('mail::message')
# Your Payroll Has Been Processed

Dear {{ $employee->name }},

Your payroll for **{{ $payroll->period }}** has been processed and is ready for payment.

## Payroll Summary
- **Basic Salary:** {{ number_format($payroll->basic_salary ?? 0, 2) }}
- **Allowances:** {{ number_format($payroll->allowances ?? 0, 2) }}
- **Overtime Pay:** {{ number_format($payroll->overtime_pay ?? 0, 2) }}
- **Gross Salary:** {{ number_format($payroll->gross_salary ?? 0, 2) }}

## Deductions
- **Deductions:** {{ number_format($payroll->deductions ?? 0, 2) }}
- **Tax:** {{ number_format($payroll->tax ?? 0, 2) }}
- **Total Deductions:** {{ number_format(($payroll->deductions ?? 0) + ($payroll->tax ?? 0), 2) }}

## Net Salary
**{{ number_format($payroll->net_salary ?? 0, 2) }}**

## Payment Method
{{ ucfirst($payroll->payment_method ?? 'Bank Transfer') }}

@if($payroll->payment_method === 'bank_transfer')
Please ensure your bank account details are correct in the system. Payment will be processed on the scheduled payroll date.
@endif

For any discrepancies or questions regarding your payroll, please contact the HR/Finance Department.

@component('mail::button', ['url' => route('payroll.employee', $employee->id)])
View Payroll Details
@endcomponent

Best regards,

{{ config('app.name') }} - Payroll Department
@endcomponent
