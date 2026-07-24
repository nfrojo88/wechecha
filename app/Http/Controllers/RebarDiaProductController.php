<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RebarDiaProduct;
use Illuminate\Http\Request;

class RebarDiaProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the rebar product mapping settings page.
     */
    public function index()
    {
        $mappings  = RebarDiaProduct::allKeyed();
        $diameters = RebarDiaProduct::standardDiameters();

        // Products suitable to link (consumable / sub_category Rebar or any)
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'sub_category']);

        return view('takeoff.rebar-products.index', compact('mappings', 'diameters', 'products'));
    }

    /**
     * Save / update all diameter → product mappings at once.
     */
    public function update(Request $request)
    {
        $request->validate([
            'mappings'                       => 'required|array',
            'mappings.*.product_id'          => 'nullable|exists:products,id',
            'mappings.*.standard_length_m'   => 'nullable|numeric|min:1|max:20',
        ]);

        $diameters = RebarDiaProduct::standardDiameters();

        foreach ($diameters as $dia => $kgPerM) {
            $row = $request->input("mappings.{$dia}", []);

            RebarDiaProduct::updateOrCreate(
                ['diameter' => $dia],
                [
                    'unit_weight_kg_per_m' => $kgPerM,
                    'product_id'           => $row['product_id'] ?? null,
                    'standard_length_m'    => $row['standard_length_m'] ?? '12',
                ]
            );
        }

        return back()->with('success', 'Rebar product mappings saved successfully.');
    }

    /**
     * Auto-seed default rebar products (creates Product records and links them).
     */
    public function seed()
    {
        (new \Database\Seeders\RebarProductSeeder())->run();
        return back()->with('success', 'Default rebar products created and linked.');
    }
}
