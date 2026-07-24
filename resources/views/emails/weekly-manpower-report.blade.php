@component('mail::message')
# Weekly Manpower Report

Dear General Manager,

Please find the weekly manpower report for the week starting **{{ $weekStarting->format('F d, Y') }}** below.

## Summary Statistics
@isset($reportData['total_manpower'])
- **Total Manpower:** {{ $reportData['total_manpower'] }} employees
- **Projects Covered:** {{ $reportData['projects_count'] ?? 'N/A' }} projects
- **Average Manpower:** {{ $reportData['average_manpower'] ?? 'N/A' }} employees/day
@endisset

## Daily Breakdown
| Date | Manpower | Status |
|------|----------|--------|
@foreach($reportData['daily'] ?? [] as $day)
| {{ $day['date'] }} | {{ $day['count'] }} | {{ $day['status'] }} |
@endforeach

## Project-wise Distribution
@foreach($reportData['projects'] ?? [] as $project)
- **{{ $project['name'] }}:** {{ $project['manpower'] }} employees
@endforeach

## Notes
{{ $reportData['notes'] ?? 'No additional notes' }}

For detailed analysis and charts, please log into the HR Dashboard.

@component('mail::button', ['url' => route('weekly-manpower.index')])
View Full Report
@endcomponent

Best regards,

{{ config('app.name') }} - HR Department
@endcomponent
