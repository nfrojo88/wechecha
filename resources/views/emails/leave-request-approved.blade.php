@component('mail::message')
# Leave Request Approved

Dear {{ $employee->name }},

We are pleased to inform you that your leave request has been **approved**.

## Leave Details
- **Leave Type:** {{ $leaveType->name }}
- **From:** {{ $leaveRequest->start_date->format('F d, Y (l)') }}
- **To:** {{ $leaveRequest->end_date->format('F d, Y (l)') }}
- **Duration:** {{ $leaveRequest->days_requested }} day(s)
- **Reason:** {{ $leaveRequest->reason }}

Please ensure all pending work is completed and handed over before your leave commences. If you need to discuss any details, please contact the HR Department.

@component('mail::button', ['url' => route('leave-requests.show', $leaveRequest->id)])
View Request Details
@endcomponent

Best regards,

{{ config('app.name') }} - HR Department
@endcomponent
