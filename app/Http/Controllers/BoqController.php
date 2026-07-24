<?php

namespace App\Http\Controllers;

use App\Models\Boq;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BoqController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Boq::class);
        
        $query = Boq::with(['project', 'creator', 'approver'])->latest();
        
        if ($request->has('project_id') && $request->project_id != '') {
            $query->where('project_id', $request->project_id);
        }
        
        $boqs = $query->paginate(15);
        $projects = Project::where('status', '!=', 'cancelled')->get();
        
        return view('boqs.index', compact('boqs', 'projects'));
    }

    public function create()
    {
        Gate::authorize('create', Boq::class);
        $projects = Project::where('status', '!=', 'cancelled')->get();
        return view('boqs.create', compact('projects'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Boq::class);
        
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'reference_number' => 'required|string|max:255|unique:boqs,reference_number',
            'description' => 'nullable|string',
        ]);
        
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';
        
        $boq = Boq::create($validated);
        
        return redirect()->route('boqs.show', $boq)->with('success', 'BOQ drafted successfully. You can now add items.');
    }

    public function show(Boq $boq)
    {
        Gate::authorize('view', $boq);
        $boq->load(['project', 'creator', 'approver', 'items.product', 'items.scheduleTask', 'items.takeoffItem']);
        $products = \App\Models\Product::where('is_active', true)->get();
        
        $scheduleTasks = \App\Models\ScheduleTask::whereHas('schedule', function ($q) use ($boq) {
            $q->where('project_id', $boq->project_id);
        })->get();

        return view('boqs.show', compact('boq', 'products', 'scheduleTasks'));
    }

    public function edit(Boq $boq)
    {
        Gate::authorize('update', $boq);
        $projects = Project::where('status', '!=', 'cancelled')->get();
        return view('boqs.edit', compact('boq', 'projects'));
    }

    public function update(Request $request, Boq $boq)
    {
        Gate::authorize('update', $boq);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'reference_number' => 'required|string|max:255|unique:boqs,reference_number,' . $boq->id,
            'description' => 'nullable|string',
        ]);
        
        $boq->update($validated);
        return redirect()->route('boqs.show', $boq)->with('success', 'BOQ header updated.');
    }

    public function destroy(Boq $boq)
    {
        Gate::authorize('delete', $boq);
        
        if ($boq->status === 'approved') {
            return back()->with('error', 'Cannot delete an approved BOQ.');
        }
        
        $boq->delete();
        return redirect()->route('boqs.index')->with('success', 'BOQ deleted successfully.');
    }

    public function approve(Boq $boq)
    {
        Gate::authorize('approve', $boq);
        
        if ($boq->status === 'approved') {
            return back()->with('info', 'BOQ is already approved.');
        }
        
        if ($boq->items()->count() === 0) {
            return back()->with('error', 'Cannot approve an empty BOQ. Add items first.');
        }
        
        $boq->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);
        
        // Update Project Contract Value automatically if approved
        if ($boq->project->contract_value == 0 || $boq->project->contract_value < $boq->total_amount) {
            $boq->project->update(['contract_value' => $boq->total_amount]);
        }
        
        return back()->with('success', 'BOQ officially approved.');
    }
}
