<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function priceIntelligence()
    {
        // Mock data for Price Intelligence
        $priceData = [
            ['material' => 'Cement (Dangote)', 'current_price' => 1250, 'trend' => 'up', 'suppliers' => 3],
            ['material' => 'Rebar 12mm', 'current_price' => 185.50, 'trend' => 'stable', 'suppliers' => 4],
            ['material' => 'Gravel (Truck)', 'current_price' => 8500, 'trend' => 'down', 'suppliers' => 2],
        ];
        
        return view('procurement.price-intelligence.index', compact('priceData'));
    }

    public function materialDemand()
    {
        // Mock data for Material Demand & Forecast
        $forecasts = [
            ['project' => 'Skyline Tower', 'material' => 'Rebar 16mm', 'required_qty' => '500 pcs', 'date_needed' => now()->addDays(5)],
            ['project' => 'Riverside Apartments', 'material' => 'Cement', 'required_qty' => '1000 bags', 'date_needed' => now()->addDays(12)],
            ['project' => 'City Mall', 'material' => 'Gravel', 'required_qty' => '50 trucks', 'date_needed' => now()->addDays(20)],
        ];

        return view('procurement.material-demand.index', compact('forecasts'));
    }
}
