<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class WeeklyManpowerReportController extends Controller
{
    /**
     * Display weekly manpower report dashboard
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfWeek()->toDateString());
        $endDate = $request->input('end_date', now()->endOfWeek()->toDateString());
        $projectId = $request->input('project_id');

        $query = DailyReport::with(['project', 'createdBy'])
            ->whereBetween('report_date', [$startDate, $endDate])
            ->where('status', 'approved');

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $reports = $query->get();

        // Generate summary statistics
        $summary = $this->generateWeeklySummary($reports, $startDate, $endDate);

        $projects = Project::where('status', 'active')->get();
        $weekNumber = now()->weekOfYear;

        return view('hr-manager.weekly-manpower-report.index', compact(
            'summary',
            'reports',
            'projects',
            'startDate',
            'endDate',
            'projectId',
            'weekNumber'
        ));
    }

    /**
     * Generate weekly manpower report by site
     */
    public function generateReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfWeek()->toDateString());
        $endDate = $request->input('end_date', now()->endOfWeek()->toDateString());

        $reports = DailyReport::with(['project', 'items', 'createdBy'])
            ->whereBetween('report_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->get();

        // Group by project
        $reportsByProject = $reports->groupBy('project_id');

        $weeklyData = [];
        foreach ($reportsByProject as $projectId => $projectReports) {
            $project = $projectReports->first()?->project;
            
            $dailyData = [];
            $startCarbon = Carbon::parse($startDate);
            $endCarbon = Carbon::parse($endDate);

            for ($date = $startCarbon; $date <= $endCarbon; $date->addDay()) {
                $dayReports = $projectReports->filter(fn($r) => $r->report_date->toDateString() === $date->toDateString());
                
                $totalManpower = $dayReports->sum('total_manpower');
                $avgManpower = $dayReports->count() > 0 ? round($totalManpower / $dayReports->count(), 2) : 0;

                $dailyData[$date->toDateString()] = [
                    'date' => $date->format('l, M d'),
                    'manpower' => $totalManpower,
                    'avg_manpower' => $avgManpower,
                    'reports_count' => $dayReports->count(),
                    'items_completed' => $dayReports->sum(fn($r) => $r->items->count()),
                ];
            }

            $weeklyData[$projectId] = [
                'project' => $project,
                'daily_data' => $dailyData,
                'total_mandays' => array_sum(array_map(fn($d) => $d['manpower'], $dailyData)),
                'avg_daily_manpower' => round(array_sum(array_map(fn($d) => $d['manpower'], $dailyData)) / count($dailyData), 2),
                'total_reports' => $projectReports->count(),
            ];
        }

        return view('hr-manager.weekly-manpower-report.generate', compact(
            'weeklyData',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Send weekly report to GM
     */
    public function sendToGM(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'gm_email' => 'required|email',
            'include_details' => 'boolean',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $gmEmail = $request->input('gm_email');
        $includeDetails = $request->boolean('include_details', false);

        // Get reports
        $reports = DailyReport::with(['project', 'items', 'createdBy'])
            ->whereBetween('report_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->get();

        $reportsByProject = $reports->groupBy('project_id');

        // Generate email data
        $emailData = [
            'start_date' => Carbon::parse($startDate)->format('M d, Y'),
            'end_date' => Carbon::parse($endDate)->format('M d, Y'),
            'week_number' => Carbon::parse($startDate)->weekOfYear,
            'generated_by' => Auth::user()->name,
            'generated_at' => now()->format('Y-m-d H:i'),
            'projects' => [],
        ];

        foreach ($reportsByProject as $projectId => $projectReports) {
            $project = $projectReports->first()?->project;

            $projectData = [
                'name' => $project?->project_name ?? $project?->name ?? 'Unknown Project',
                'total_mandays' => $projectReports->sum('total_manpower'),
                'avg_daily_manpower' => round($projectReports->avg('total_manpower'), 2),
                'peak_manpower' => $projectReports->max('total_manpower'),
                'min_manpower' => $projectReports->min('total_manpower'),
                'total_reports' => $projectReports->count(),
                'total_items_completed' => $projectReports->sum(fn($r) => $r->items->count()),
            ];

            if ($includeDetails) {
                $projectData['daily_breakdown'] = $projectReports
                    ->groupBy(fn($r) => $r->report_date->toDateString())
                    ->map(fn($group) => [
                        'date' => $group->first()->report_date->format('M d, Y'),
                        'manpower' => $group->sum('total_manpower'),
                        'reports' => $group->count(),
                    ])
                    ->values()
                    ->toArray();
            }

            $emailData['projects'][] = $projectData;
        }

        // Send email
        try {
            Mail::send('emails.weekly-manpower-report', $emailData, function ($message) use ($gmEmail) {
                $message->to($gmEmail)
                    ->subject('Weekly Manpower Report - ' . now()->format('Y-m-d'));
            });

            // Log the action
            \App\Models\ActivityTimeLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Weekly manpower report sent to GM',
                'entered_at' => now(),
            ]);

            return back()->with('success', "Weekly manpower report sent to $gmEmail successfully.");
        } catch (\Exception $e) {
            return back()->withErrors('Failed to send report: ' . $e->getMessage());
        }
    }

    /**
     * Export weekly report as CSV
     */
    public function exportCSV(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfWeek()->toDateString());
        $endDate = $request->input('end_date', now()->endOfWeek()->toDateString());

        $reports = DailyReport::with(['project', 'items', 'createdBy'])
            ->whereBetween('report_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->get();

        $reportsByProject = $reports->groupBy('project_id');

        $csvData = [
            ['WEEKLY MANPOWER REPORT', '', '', '', ''],
            ['Week:', Carbon::parse($startDate)->weekOfYear, 'From:', $startDate, 'To:', $endDate],
            ['Generated:', now()->format('Y-m-d H:i'), '', '', ''],
            [],
        ];

        foreach ($reportsByProject as $projectId => $projectReports) {
            $project = $projectReports->first()?->project;
            
            $csvData[] = [
                'PROJECT: ' . ($project?->project_name ?? $project?->name ?? 'Unknown'),
                '',
                '',
                '',
                '',
            ];
            $csvData[] = ['Date', 'Manpower', 'Reports', 'Items Completed', 'Reported By'];

            $projectReports->groupBy(fn($r) => $r->report_date->toDateString())
                ->each(function ($group) use (&$csvData) {
                    $csvData[] = [
                        $group->first()->report_date->format('Y-m-d'),
                        $group->sum('total_manpower'),
                        $group->count(),
                        $group->sum(fn($r) => $r->items->count()),
                        $group->first()->createdBy->name ?? 'System',
                    ];
                });

            $csvData[] = [
                'TOTAL',
                $projectReports->sum('total_manpower'),
                $projectReports->count(),
                $projectReports->sum(fn($r) => $r->items->count()),
                '',
            ];
            $csvData[] = [];
        }

        $fileName = 'weekly-manpower-report-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($csvData) {
            $handle = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ]);
    }

    /**
     * Generate summary statistics
     */
    private function generateWeeklySummary($reports, $startDate, $endDate)
    {
        $summary = [
            'total_reports' => $reports->count(),
            'total_mandays' => $reports->sum('total_manpower'),
            'avg_daily_manpower' => $reports->count() > 0 ? round($reports->avg('total_manpower'), 2) : 0,
            'peak_manpower_day' => $reports->max('total_manpower'),
            'min_manpower_day' => $reports->min('total_manpower') > 0 ? $reports->min('total_manpower') : 0,
            'projects_count' => $reports->groupBy('project_id')->count(),
            'total_items_completed' => $reports->sum(fn($r) => $r->items->count()),
            'avg_items_per_report' => $reports->count() > 0 ? round($reports->sum(fn($r) => $r->items->count()) / $reports->count(), 2) : 0,
        ];

        // Daily breakdown
        $startCarbon = Carbon::parse($startDate);
        $endCarbon = Carbon::parse($endDate);
        $summary['daily_breakdown'] = [];

        for ($date = $startCarbon; $date <= $endCarbon; $date->addDay()) {
            $dayReports = $reports->filter(fn($r) => $r->report_date->toDateString() === $date->toDateString());
            
            $summary['daily_breakdown'][$date->toDateString()] = [
                'date' => $date->format('l'),
                'manpower' => $dayReports->sum('total_manpower'),
                'reports' => $dayReports->count(),
            ];
        }

        // By project
        $summary['by_project'] = [];
        $reports->groupBy('project_id')->each(function ($group) use (&$summary) {
            $project = $group->first()->project;
            $summary['by_project'][] = [
                'name' => $project?->project_name ?? $project?->name ?? 'Unknown',
                'manpower' => $group->sum('total_manpower'),
                'reports' => $group->count(),
                'avg_daily' => round($group->avg('total_manpower'), 2),
            ];
        });

        return $summary;
    }
}
