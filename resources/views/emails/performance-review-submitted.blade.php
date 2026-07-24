@component('mail::message')
# Performance Review Submitted for Approval

Dear HR Manager,

A performance review has been submitted by {{ $reviewer->name }} and requires your approval.

## Employee Information
- **Name:** {{ $employee->name }}
- **Code:** {{ $employee->code }}
- **Review Period:** {{ $review->review_period->format('F Y') }}

## Performance Scores
- **Technical Skills:** {{ $review->technical_skills_score }}/5
- **Soft Skills:** {{ $review->soft_skills_score }}/5
- **Attendance:** {{ $review->attendance_score }}/5
- **Productivity:** {{ $review->productivity_score }}/5
- **Communication:** {{ $review->communication_score }}/5
- **Teamwork:** {{ $review->teamwork_score }}/5
- **Overall Score:** {{ $review->overall_score }}/5

## Reviewer's Comments
{{ $review->comments ?? 'No additional comments' }}

@component('mail::button', ['url' => route('performance-dashboard.show-review', $review->id)])
Review Performance Details
@endcomponent

Please review and approve this performance review in the system.

Best regards,

{{ config('app.name') }} - Performance Management System
@endcomponent
