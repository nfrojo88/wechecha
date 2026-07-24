<?php
namespace App\Http\Controllers;

use App\Models\StandardWork;
use App\Models\StandardWorkMaterial;
use App\Models\StandardWorkManpower;
use App\Models\StandardWorkEquipment;
use App\Models\Product;
use App\Models\EquipmentMaster;
use Illuminate\Http\Request;

class StandardWorkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:planning_manager|planning|technical_manager|admin|global_admin');
    }

    public function index()
    {
        $works = StandardWork::with('materials', 'manpower', 'equipment', 'creator')
            ->latest()
            ->paginate(20);
        return view('standard_works.index', compact('works'));
    }

    public function create()
    {
        $products      = Product::where('is_active', true)->orderBy('name')->get(['id','name','unit','category']);
        $equipmentList = EquipmentMaster::where('is_active', true)->orderBy('name')->get(['id','name','unit']);
        return view('standard_works.create', compact('products', 'equipmentList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'unit'        => 'required|string|max:50',
            'description' => 'nullable|string',

            // Materials — optional, zero qty OK
            'materials'                   => 'nullable|array',
            'materials.*.material_name'   => 'nullable|string|max:255',
            'materials.*.quantity'        => 'nullable|numeric|min:0',
            'materials.*.unit'            => 'nullable|string|max:50',

            // Manpower — optional, zero qty OK
            'manpower'                    => 'nullable|array',
            'manpower.*.role'             => 'nullable|string|max:255',
            'manpower.*.quantity'         => 'nullable|numeric|min:0',
            'manpower.*.unit'             => 'nullable|string|max:50',

            // Equipment — optional, zero qty OK
            'equipment'                   => 'nullable|array',
            'equipment.*.equipment_name'  => 'nullable|string|max:255',
            'equipment.*.quantity'        => 'nullable|numeric|min:0',
            'equipment.*.unit'            => 'nullable|string|max:50',
        ]);

        $work = StandardWork::create([
            'category'    => 'Mixed',
            'name'        => $request->name,
            'unit'        => $request->unit,
            'description' => $request->description,
            'created_by'  => auth()->id(),
        ]);

        // Save materials — include zero-quantity rows if name is filled
        foreach ($request->input('materials', []) as $row) {
            if (!empty($row['material_name'])) {
                $work->materials()->create([
                    'material_name' => $row['material_name'],
                    'quantity'      => (float)($row['quantity'] ?? 0),
                    'unit'          => $row['unit'] ?? '',
                ]);
            }
        }

        // Save manpower — include zero-quantity rows if role is filled
        foreach ($request->input('manpower', []) as $row) {
            if (!empty($row['role'])) {
                $work->manpower()->create([
                    'role'     => $row['role'],
                    'quantity' => (float)($row['quantity'] ?? 0),
                    'unit'     => $row['unit'] ?? '',
                ]);
            }
        }

        // Save equipment — include zero-quantity rows if name is filled
        foreach ($request->input('equipment', []) as $row) {
            if (!empty($row['equipment_name'])) {
                $work->equipment()->create([
                    'equipment_name' => $row['equipment_name'],
                    'quantity'       => (float)($row['quantity'] ?? 0),
                    'unit'           => $row['unit'] ?? '',
                ]);
            }
        }

        return redirect()->route('standard-works.show', $work)
            ->with('success', 'Standard Work created successfully.');
    }

    public function show(StandardWork $standardWork)
    {
        $standardWork->load('materials', 'manpower', 'equipment', 'creator');
        return view('standard_works.show', compact('standardWork'));
    }

    public function destroy(StandardWork $standardWork)
    {
        $standardWork->materials()->delete();
        $standardWork->manpower()->delete();
        $standardWork->equipment()->delete();
        $standardWork->delete();
        return redirect()->route('standard-works.index')
            ->with('success', 'Standard Work deleted.');
    }
}
