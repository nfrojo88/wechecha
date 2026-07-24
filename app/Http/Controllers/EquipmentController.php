<?php

namespace App\Http\Controllers;

use App\Models\EquipmentMaster;
use App\Models\EquipmentProductivity;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipment = EquipmentMaster::latest()->get();
        return view('operational.equipment.index', compact('equipment'));
    }

    public function create()
    {
        return view('operational.equipment.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:equipment_masters,code',
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:20',
            'hourly_rate' => 'required|numeric',
            'daily_rate' => 'required|numeric',
        ]);
        
        EquipmentMaster::create($data);
        return redirect()->route('equipment.index')->with('success', 'Equipment added successfully.');
    }

    public function show(EquipmentMaster $equipment)
    {
        $equipment->load(['productivities.project', 'productivities.recordedBy']);
        $projects = \App\Models\Project::where('status', 'active')->get();
        return view('operational.equipment.show', compact('equipment', 'projects'));
    }

    public function logProductivity(Request $request, EquipmentMaster $equipment)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'work_date' => 'required|date',
            'hours_operated' => 'required|numeric',
            'task_performed' => 'nullable|string|max:255',
        ]);
        $data['equipment_id'] = $equipment->id;
        $data['recorded_by'] = auth()->id();
        
        EquipmentProductivity::create($data);
        return redirect()->route('equipment.show', $equipment)->with('success', 'Productivity logged successfully.');
    }
}
