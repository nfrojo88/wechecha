@component('mail::message')
# Leave Request Rejected

Dear {{ $employee->name }},

Unfortunately, your leave request for the period **{{ $leaveRequest->start_date->format('F d, Y') }} to {{ $leaveRequest->end_date->format('F d, Y') }}** has been **rejected**.

## Rejection Reason
{{ $leaveRequest->rejection_reason }}

## What to Do Next
Please review the rejection reason and:
1. If you believe this decision is incorrect, contact the HR Department to discuss
2. Consider submitting a new request for alternative dates if appropriate

@component('mail::button', ['url' => route('leave-requests.show', $leaveRequest->id)])
View Request Details
@endcomponent

For any questions, please reach out to the HR Department.

Best regards,

{{ config('app.name') }} - HR Department
@endcomponent
