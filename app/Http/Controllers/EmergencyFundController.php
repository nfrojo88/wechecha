<?php

namespace App\Http\Controllers;

use App\Models\EmergencyFund;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyFundController extends Controller
{
    public function index()
    {
        $funds = EmergencyFund::with(['project', 'requestedBy'])->latest()->paginate(20);
        return view('finance.emergency-funds.index', compact('funds'));
    }

    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        return view('finance.emergency-funds.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'       => 'required|exists:projects,id',
            'requested_amount' => 'required|numeric|min:1',
            'justification'    => 'required|string',
        ]);

        $data['requested_by'] = Auth::id();
        $data['status']       = 'pending';

        EmergencyFund::create($data);
        return redirect()->route('emergency-funds.index')->with('success', 'Emergency Fund Request submitted.');
    }

    public function show(EmergencyFund $emergencyFund)
    {
        $emergencyFund->load(['project', 'requestedBy', 'approvedBy']);
        return view('finance.emergency-funds.show', compact('emergencyFund'));
    }

    public function approve(EmergencyFund $emergencyFund)
    {
        $emergencyFund->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Emergency Fund Request approved.');
    }

    public function reject(EmergencyFund $emergencyFund)
    {
        $emergencyFund->update(['status' => 'rejected']);
        return back()->with('success', 'Emergency Fund Request rejected.');
    }
}
