<?php
namespace App\Http\Controllers;
use App\Models\WeeklyPlanDispatch;
use App\Services\WeeklyDispatchService;
use Illuminate\Http\Request;

class WeeklyDispatchController extends Controller {
    public function __construct(private WeeklyDispatchService $dispatchService) {
        $this->middleware('auth');
    }
    public function index() {
        $dispatches = WeeklyPlanDispatch::with('project', 'dispatchedTo')->latest()->paginate(20);
        return view('weekly_dispatches.index', compact('dispatches'));
    }
    public function show(WeeklyPlanDispatch $weekly_dispatch) {
        $weekly_dispatch->load('dispatchTasks.task');
        return view('weekly_dispatches.show', compact('weekly_dispatch'));
    }
}
