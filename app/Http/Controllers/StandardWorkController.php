<?php
namespace App\Http\Controllers;

use App\Models\StandardWork;
use App\Models\StandardWorkMaterial;
use App\Models\StandardWorkManpower;
use App\Models\StandardWorkEquipment;
use App\Models\Product;
use App\Models\EquipmentMaster;
use App\Models\ManpowerRole;
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
        $products       = Product::where('is_active', true)->orderBy('name')->get(['id','name','unit','category']);
        
        // Combine EquipmentMaster and Products in "Fixed Asset" category
        $fixedAssets    = Product::where('is_active', true)
                            ->where('category', 'Fixed Asset')
                            ->orderBy('name')
                            ->get(['id', 'name', 'unit'])
                            ->map(fn($p) => (object)['id' => $p->id, 'name' => $p->name . ' (Fixed Asset)', 'unit' => $p->unit]);

        $equipmentMasterList = EquipmentMaster::where('is_active', true)
                                ->orderBy('name')
                                ->get(['id','name','unit'])
                                ->map(fn($e) => (object)['id' => $e->id, 'name' => $e->name, 'unit' => $e->unit]);

        $equipmentList = $equipmentMasterList->concat($fixedAssets)->sortBy('name')->values();

        $manpowerRoles  = ManpowerRole::orderBy('name')->get();
        return view('standard_works.create', compact('products', 'equipmentList', 'manpowerRoles'));
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

            // Scientific Manpower — optional, zero qty OK
            'scientific_manpower'                => 'nullable|array',
            'scientific_manpower.*.role'         => 'nullable|string|max:255',
            'scientific_manpower.*.quantity'     => 'nullable|numeric|min:0',
            'scientific_manpower.*.unit'         => 'nullable|string|max:50',
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

        // Save scientific manpower — stored in the same table with type='scientific'
        foreach ($request->input('scientific_manpower', []) as $row) {
            if (!empty($row['role'])) {
                $work->scientificManpower()->create([
                    'role'     => $row['role'],
                    'quantity' => (float)($row['quantity'] ?? 0),
                    'unit'     => $row['unit'] ?? '',
                    'type'     => 'scientific',
                ]);
            }
        }

        return redirect()->route('standard-works.show', $work)
            ->with('success', 'Standard Work created successfully.');
    }

    public function show(StandardWork $standardWork)
    {
        $standardWork->load('materials', 'manpower', 'scientificManpower', 'equipment', 'creator');
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
