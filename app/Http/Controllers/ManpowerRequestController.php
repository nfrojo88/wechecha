<?php

namespace App\Http\Controllers;

use App\Models\ManpowerRequest;
use App\Models\Project;
use App\Models\ErpPlanTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManpowerRequestController extends Controller
{
    public function index()
    {
        $requests = ManpowerRequest::with(['project', 'requestedBy'])->latest()->paginate(20);
        return view('hr.manpower-requests.index', compact('requests'));
    }

    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        return view('hr.manpower-requests.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'    => 'required|exists:projects,id',
            'task_id'       => 'nullable|exists:erp_plan_tasks,id',
            'type'          => 'required|in:new_hire,replacement,temporary',
            'required_date' => 'required|date',
            'requirements'  => 'nullable|string',
            'notes'         => 'nullable|string',
            'items'         => 'required|array|min:1',
            'items.*.role_title' => 'required|string|max:255',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.skill_level'=> 'required|in:unskilled,semi_skilled,skilled,professional',
        ]);

        $mp = ManpowerRequest::create([
            'project_id'   => $data['project_id'],
            'requested_by' => Auth::id(),
            'task_id'      => $data['task_id'] ?? null,
            'type'         => $data['type'],
            'required_date'=> $data['required_date'],
            'requirements' => $data['requirements'] ?? null,
            'notes'        => $data['notes'] ?? null,
            'status'       => 'pending',
        ]);

        foreach ($data['items'] as $item) {
            $mp->items()->create($item);
        }

        return redirect()->route('manpower-requests.index')->with('success', 'Manpower Request submitted.');
    }

    public function show(ManpowerRequest $manpowerRequest)
    {
        $manpowerRequest->load(['project', 'requestedBy', 'task', 'items']);
        return view('hr.manpower-requests.show', compact('manpowerRequest'));
    }
}
