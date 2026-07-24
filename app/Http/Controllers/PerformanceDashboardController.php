<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PerformanceReview;
use App\Models\PerformanceMetric;
use App\Models\PerformanceGoal;
use App\Models\CompetencyAssessment;
use App\Models\EmployeeAchievement;
use App\Models\Competency;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display performance dashboard
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', PerformanceReview::class);

        $query = PerformanceReview::with(['employee', 'reviewer']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reviews = $query->orderBy('review_period', 'desc')->paginate(15);

        $stats = [
            'total_reviewed' => PerformanceReview::where('status', 'approved')->count(),
            'pending_review' => PerformanceReview::where('status', 'draft')->count(),
            'submitted_for_approval' => PerformanceReview::where('status', 'submitted')->count(),
            'avg_score' => PerformanceReview::where('status', 'approved')->avg('overall_score'),
        ];

        $employees = Employee::where('status', 'active')->orderBy('full_name')->get();

        return view('hr-manager.performance-dashboard.index', compact('reviews', 'stats', 'employees'));
    }

    /**
     * Show employee performance details
     */
    public function showEmployee(Employee $employee)
    {
        $this->authorize('viewAny', PerformanceReview::class);

        $employee->load(['performanceReviews', 'performanceGoals', 'performanceMetrics', 'competencyAssessments.competency', 'achievements']);

        // Get latest review
        $latestReview = $employee->performanceReviews()
            ->where('status', 'approved')
            ->orderBy('review_period', 'desc')
            ->first();

        // Get active goals
        $activeGoals = $employee->performanceGoals()
            ->whereIn('status', ['not_started', 'in_progress'])
            ->get();

        // Get recent achievements
        $recentAchievements = $employee->achievements()
            ->orderBy('achievement_date', 'desc')
            ->limit(5)
            ->get();

        // Competency assessment summary
        $competencies = $employee->competencyAssessments()
            ->with('competency')
            ->get();

        return view('hr-manager.performance-dashboard.employee-details', compact(
            'employee',
            'latestReview',
            'activeGoals',
            'recentAchievements',
            'competencies'
        ));
    }

    /**
     * Create performance review
     */
    public function createReview(Request $request)
    {
        $this->authorize('create', PerformanceReview::class);

        $employees = Employee::where('status', 'active')->orderBy('full_name')->get();

        return view('hr-manager.performance-dashboard.create-review', compact('employees'));
    }

    /**
     * Store performance review
     */
    public function storeReview(Request $request)
    {
        $this->authorize('create', PerformanceReview::class);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'review_period' => 'required|date',
            'technical_skills_score' => 'required|numeric|between:1,5',
            'soft_skills_score' => 'required|numeric|between:1,5',
            'attendance_score' => 'required|numeric|between:1,5',
            'productivity_score' => 'required|numeric|between:1,5',
            'communication_score' => 'required|numeric|between:1,5',
            'teamwork_score' => 'required|numeric|between:1,5',
            'comments' => 'nullable|string|max:1000',
            'strengths' => 'nullable|string|max:500',
            'areas_for_improvement' => 'nullable|string|max:500',
            'development_plan' => 'nullable|string|max:1000',
        ]);

        // Calculate overall score as average
        $scores = [
            $validated['technical_skills_score'],
            $validated['soft_skills_score'],
            $validated['attendance_score'],
            $validated['productivity_score'],
            $validated['communication_score'],
            $validated['teamwork_score'],
        ];

        $validated['overall_score'] = array_sum($scores) / count($scores);
        $validated['reviewer_id'] = Auth::id();
        $validated['status'] = 'draft';

        $review = PerformanceReview::create($validated);

        return redirect()->route('performance-dashboard.show-review', $review->id)
            ->with('success', 'Performance review created');
    }

    /**
     * Show performance review
     */
    public function showReview(PerformanceReview $review)
    {
        $this->authorize('view', $review);

        $review->load(['employee', 'reviewer']);

        return view('hr-manager.performance-dashboard.show-review', compact('review'));
    }

    /**
     * Submit review for approval
     */
    public function submitReview(PerformanceReview $review)
    {
        $this->authorize('update', $review);

        if ($review->status !== 'draft') {
            return back()->withErrors(['status' => 'Only draft reviews can be submitted']);
        }

        $review->update(['status' => 'submitted']);

        return back()->with('success', 'Review submitted for approval');
    }

    /**
     * Approve review
     */
    public function approveReview(PerformanceReview $review)
    {
        $this->authorize('approve', $review);

        if ($review->status !== 'submitted') {
            return back()->withErrors(['status' => 'Only submitted reviews can be approved']);
        }

        $review->update([
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Performance review approved');
    }

    /**
     * Reject review
     */
    public function rejectReview(Request $request, PerformanceReview $review)
    {
        $this->authorize('approve', $review);

        if ($review->status !== 'submitted') {
            return back()->withErrors(['status' => 'Only submitted reviews can be rejected']);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $review->update(['status' => 'draft']);

        return back()->with('success', 'Review returned for revision: ' . $validated['rejection_reason']);
    }

    /**
     * Get performance analytics
     */
    public function analytics(Request $request)
    {
        $this->authorize('viewAny', PerformanceReview::class);

        $fromDate = $request->filled('from_date') ? $request->from_date : Carbon::now()->subMonths(12)->toDateString();
        $toDate = $request->filled('to_date') ? $request->to_date : Carbon::now()->toDateString();

        $reviews = PerformanceReview::where('status', 'approved')
            ->whereBetween('review_period', [$fromDate, $toDate])
            ->with('employee')
            ->get();

        $scoreDistribution = [
            'excellent' => $reviews->where('overall_score', '>=', 4.5)->count(),
            'good' => $reviews->whereBetween('overall_score', [3.5, 4.49])->count(),
            'satisfactory' => $reviews->whereBetween('overall_score', [2.5, 3.49])->count(),
            'needs_improvement' => $reviews->where('overall_score', '<', 2.5)->count(),
        ];

        $departmentStats = [];
        foreach ($reviews->groupBy(fn($r) => $r->employee->department) as $dept => $deptReviews) {
            $departmentStats[$dept] = [
                'count' => $deptReviews->count(),
                'avg_score' => $deptReviews->avg('overall_score'),
            ];
        }

        return view('hr-manager.performance-dashboard.analytics', compact(
            'reviews',
            'scoreDistribution',
            'departmentStats',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Export performance report
     */
    public function exportReport(Request $request)
    {
        $this->authorize('viewAny', PerformanceReview::class);

        $query = PerformanceReview::with('employee')->where('status', 'approved');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $reviews = $query->get();

        $fileName = 'performance-report-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($reviews) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Employee', 'Period', 'Overall Score', 'Technical', 'Soft Skills', 'Attendance', 'Productivity', 'Rating']);

            foreach ($reviews as $review) {
                fputcsv($file, [
                    $review->employee->name,
                    $review->review_period->format('Y-m'),
                    $review->overall_score,
                    $review->technical_skills_score,
                    $review->soft_skills_score,
                    $review->attendance_score,
                    $review->productivity_score,
                    $review->overall_rating,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
