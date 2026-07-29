<?php

namespace App\Http\Controllers;

use App\Models\EquipmentMaster;
use App\Models\EquipmentFixedAssetUnit;
use App\Models\EquipmentProductivity;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipmentMasterList = EquipmentMaster::withCount(['fixedAssetUnits as total_units'])
            ->with('fixedAssetUnits')
            ->latest()->get();

        // Fixed assets from Product catalog (category = Fixed Asset) not yet linked
        $fixedAssetProducts = \App\Models\Product::where('category', 'Fixed Asset')->latest()->get();

        return view('operational.equipment.index', compact('equipmentMasterList', 'fixedAssetProducts'));
    }

    public function create()
    {
        return view('operational.equipment.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|unique:equipment_masters,code',
            'category'    => 'nullable|string|max:100',
            'unit'        => 'required|string|max:20',
            'hourly_rate' => 'required|numeric',
            'daily_rate'  => 'required|numeric',
        ]);

        $eq = EquipmentMaster::create($data);

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment type "' . $eq->name . '" created. Now you can link individual fixed asset units.');
    }

    public function show(EquipmentMaster $equipment)
    {
        $equipment->load(['productivities.project', 'productivities.recordedBy', 'fixedAssetUnits.product']);
        $projects          = \App\Models\Project::where('status', 'active')->get();
        $fixedAssetProducts = \App\Models\Product::where('category', 'Fixed Asset')->latest()->get();

        return view('operational.equipment.show', compact('equipment', 'projects', 'fixedAssetProducts'));
    }

    /** Store a new individual fixed asset unit under an equipment type */
    public function storeUnit(Request $request, EquipmentMaster $equipment)
    {
        $data = $request->validate([
            'asset_name'     => 'required|string|max:255',
            'plate_number'   => 'nullable|string|max:100',
            'chassis_number' => 'nullable|string|max:100',
            'model'          => 'nullable|string|max:100',
            'year'           => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'condition'      => 'nullable|in:good,fair,maintenance',
            'status'         => 'nullable|in:available,on_site,maintenance,retired',
            'current_location' => 'nullable|string|max:255',
            'product_id'     => 'nullable|exists:products,id',
            'notes'          => 'nullable|string|max:500',
        ]);

        $data['equipment_master_id'] = $equipment->id;
        $data['created_by']          = auth()->id();
        $data['status']              = $data['status'] ?? 'available';
        $data['condition']           = $data['condition'] ?? 'good';

        EquipmentFixedAssetUnit::create($data);

        return redirect()->route('equipment.show', $equipment)
            ->with('success', 'Fixed asset unit "' . $data['asset_name'] . '" linked successfully!');
    }

    /** Update an individual unit's status / plate etc */
    public function updateUnit(Request $request, EquipmentMaster $equipment, EquipmentFixedAssetUnit $unit)
    {
        $data = $request->validate([
            'plate_number'     => 'nullable|string|max:100',
            'chassis_number'   => 'nullable|string|max:100',
            'model'            => 'nullable|string|max:100',
            'year'             => 'nullable|integer',
            'condition'        => 'nullable|in:good,fair,maintenance',
            'status'           => 'nullable|in:available,on_site,maintenance,retired',
            'current_location' => 'nullable|string|max:255',
            'notes'            => 'nullable|string',
        ]);

        $unit->update($data);

        return redirect()->route('equipment.show', $equipment)
            ->with('success', 'Unit updated successfully.');
    }

    /** Remove a linked unit */
    public function destroyUnit(EquipmentMaster $equipment, EquipmentFixedAssetUnit $unit)
    {
        $unit->delete();
        return redirect()->route('equipment.show', $equipment)
            ->with('success', 'Asset unit removed.');
    }

    public function logProductivity(Request $request, EquipmentMaster $equipment)
    {
        $data = $request->validate([
            'project_id'     => 'required|exists:projects,id',
            'work_date'      => 'required|date',
            'hours_operated' => 'required|numeric',
            'task_performed' => 'nullable|string|max:255',
        ]);
        $data['equipment_id']  = $equipment->id;
        $data['recorded_by']   = auth()->id();
        EquipmentProductivity::create($data);
        return back()->with('success', 'Productivity logged.');
    }
}
