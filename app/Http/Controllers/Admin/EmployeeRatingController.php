<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeRating;
use Illuminate\Http\Request;

class EmployeeRatingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || !$user->roles || !$user->roles->whereIn('name', ['global_admin', 'admin'])->count()) {
                abort(403, 'Access denied. Admin only.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $employees = Employee::where('status', 'active')
            ->withAvg('ratings as avg_rating', 'rating')
            ->withCount('ratings')
            ->with(['ratings' => fn($q) => $q->latest()->take(3)])
            ->orderBy('full_name')
            ->paginate(20);

        return view('admin.employees.ratings', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'rating'      => 'required|integer|min:1|max:5',
            'comment'     => 'nullable|string|max:1000',
            'category'    => 'required|in:overall,attendance,performance,attitude',
            'period'      => 'nullable|string|max:50',
        ]);

        $period = $request->period ?: now()->format('F Y');

        EmployeeRating::create([
            'employee_id' => $request->employee_id,
            'rated_by'    => auth()->id(),
            'rating'      => $request->rating,
            'comment'     => $request->comment,
            'category'    => $request->category,
            'period'      => $period,
        ]);

        return back()->with('success', 'Employee rating submitted successfully.');
    }
}
