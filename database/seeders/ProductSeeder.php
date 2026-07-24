<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Nails - Consumables
            [
                'name' => 'Nail #6',
                'sku' => 'MAT-0001',
                'unit' => 'Cartoon',
                'category' => 'Consumable',
                'unit_price' => 1300.00,
                'selling_price' => 0.00,
                'max_stock' => 10.00,
                'reorder_level' => 20,
            ],
            [
                'name' => 'Nail #8',
                'sku' => 'MAT-0003',
                'unit' => 'packet',
                'category' => 'Consumable',
                'unit_price' => 1300.00,
                'max_stock' => 20.00,
                'reorder_level' => 20,
            ],
            
            // Metals - Consumables
            [
                'name' => 'RHS 30*30*3',
                'sku' => 'MAT-0037',
                'unit' => 'PCS',
                'category' => 'Consumable',
                'max_stock' => 70.00,
                'reorder_level' => 20,
            ],
            [
                'name' => 'Angle Metal # 40*4mm',
                'sku' => 'MAT-0035',
                'unit' => 'PCS',
                'category' => 'Consumable',
                'max_stock' => 1000.00,
                'reorder_level' => 0,
            ],
            
            // Rebars - Consumables
            [
                'name' => 'Rebar Ø8mm',
                'sku' => 'MAT-0097',
                'unit' => 'Berga',
                'standard_length' => 12.00,
                'category' => 'Consumable',
                'unit_price' => 1161.00,
                'max_stock' => 100.00,
                'reorder_level' => 0,
            ],
            [
                'name' => 'Rebar Ø10mm',
                'sku' => 'MAT-0100',
                'unit' => 'Berga',
                'standard_length' => 12.00,
                'category' => 'Consumable',
                'unit_price' => 1814.00,
                'max_stock' => 100.00,
                'reorder_level' => 0,
            ],
            [
                'name' => 'Rebar Ø12mm',
                'sku' => 'MAT-0095',
                'unit' => 'Berga',
                'standard_length' => 12.00,
                'category' => 'Consumable',
                'unit_price' => 2611.00,
                'max_stock' => 100.00,
                'reorder_level' => 0,
            ],
            [
                'name' => 'Rebar Ø16',
                'sku' => 'MAT-0106',
                'unit' => 'Berga',
                'standard_length' => 12.00,
                'category' => 'Consumable',
                'unit_price' => 4642.00,
                'max_stock' => 100.00,
                'reorder_level' => 0,
            ],
            
            // Cement & Aggregates
            [
                'name' => 'Cement',
                'sku' => 'MAT-0042',
                'unit' => 'Quintal',
                'category' => 'Consumable',
                'unit_price' => 2100.00,
                'max_stock' => 100.00,
                'reorder_level' => 25,
            ],
            [
                'name' => 'Fine Aggregate',
                'sku' => 'MAT-0202',
                'unit' => 'm3',
                'category' => 'Consumable',
                'unit_price' => 6875.00,
                'reorder_level' => 20,
            ],
            [
                'name' => 'Sand',
                'sku' => 'MAT-0056',
                'unit' => 'm3',
                'category' => 'Consumable',
                'unit_price' => 7500.00,
                'max_stock' => 100.00,
                'reorder_level' => 0,
            ],
            
            // Pipes - PVC
            [
                'name' => 'PVC Dia. 50mm',
                'sku' => 'MAT-0639',
                'unit' => 'm',
                'standard_length' => 100.00,
                'category' => 'Consumable',
                'unit_price' => 110.00,
                'max_stock' => 100.00,
                'reorder_level' => 20,
            ],
            [
                'name' => 'PVC Dia. 110mm',
                'sku' => 'MAT-0817',
                'unit' => 'm',
                'standard_length' => 6.00,
                'category' => 'Consumable',
                'unit_price' => 210.00,
                'max_stock' => 100.00,
                'reorder_level' => 20,
            ],
            
            // Pipes - PPR
            [
                'name' => 'PPR Pipe #25',
                'sku' => 'MAT-0854',
                'unit' => 'm',
                'category' => 'Consumable',
                'max_stock' => 100.00,
                'reorder_level' => 20,
            ],
            [
                'name' => 'PPR Pipe #32',
                'sku' => 'MAT-0855',
                'unit' => 'm',
                'standard_length' => 4.00,
                'category' => 'Consumable',
                'max_stock' => 100.00,
                'reorder_level' => 20,
            ],
            
            // Electrical - Wires & Cables
            [
                'name' => 'Wire 2.5mm',
                'sku' => 'MAT-0870',
                'unit' => 'Metre',
                'standard_length' => 100.00,
                'category' => 'Consumable',
                'unit_price' => 152.00,
                'max_stock' => 100.00,
                'reorder_level' => 20,
            ],
            [
                'name' => 'Cable 3*2.5mm',
                'sku' => 'MAT-0875',
                'unit' => 'Metre',
                'category' => 'Fixed Asset',
                'unit_price' => 575.00,
                'max_stock' => 100.00,
                'reorder_level' => 20,
            ],
            
            // Electrical - Breakers
            [
                'name' => 'Breaker 16Amp 1ph',
                'sku' => 'MAT-0223',
                'unit' => 'No.',
                'category' => 'Consumable',
                'unit_price' => 480.00,
                'max_stock' => 1.00,
                'reorder_level' => 20,
            ],
            [
                'name' => '3phase Breaker 100A',
                'sku' => 'MAT-0738',
                'unit' => 'PCS',
                'category' => 'Consumable',
                'unit_price' => 7200.00,
                'max_stock' => 100.00,
                'reorder_level' => 20,
            ],
            
            // Electrical - LED & Lighting
            [
                'name' => 'LED Panel Light 60*60cm 48w',
                'sku' => 'MAT-0707',
                'unit' => 'PCS',
                'category' => 'Consumable',
                'unit_price' => 3500.00,
                'max_stock' => 100.00,
                'reorder_level' => 20,
            ],
            
            // Ceramics & Tiles
            [
                'name' => 'Ceramic 60*60cm',
                'sku' => 'MAT-0075',
                'unit' => 'PCS',
                'category' => 'Consumable',
                'unit_price' => 2500.00,
                'max_stock' => 100.00,
                'reorder_level' => 1,
            ],
            [
                'name' => 'Porcelain 60*60*10mm',
                'sku' => 'MAT-0764',
                'unit' => 'M2',
                'category' => 'Consumable',
                'max_stock' => 100.00,
                'reorder_level' => 20,
            ],
            
            // Paints
            [
                'name' => 'White Quartz',
                'sku' => 'MAT-0172',
                'unit' => 'Can',
                'category' => 'Consumable',
                'unit_price' => 2504.00,
                'max_stock' => 100.00,
                'reorder_level' => 0,
            ],
            [
                'name' => '#118 Off White Paint',
                'sku' => 'MAT-1218',
                'unit' => 'Gallon',
                'category' => 'Consumable',
                'unit_price' => 1429.00,
                'max_stock' => 100.00,
                'reorder_level' => 20,
            ],
            
            // Sanitary
            [
                'name' => 'Hand Wash Basin (HWB 45*55cm)',
                'sku' => 'MAT-0110',
                'unit' => 'PCS',
                'category' => 'Consumable',
                'unit_price' => 12500.00,
                'max_stock' => 100.00,
                'reorder_level' => 0,
            ],
            [
                'name' => 'WC',
                'sku' => 'MAT-0233',
                'unit' => 'pcs',
                'category' => 'Consumable',
                'unit_price' => 28500.00,
                'reorder_level' => 20,
            ],
            
            // Fixed Assets - Equipment
            [
                'name' => 'Welding Machine',
                'sku' => 'MAT-0312',
                'unit' => '',
                'category' => 'Fixed Asset',
                'max_stock' => 100.00,
                'reorder_level' => 20,
                'equipment_condition' => 'Good',
                'current_location' => 'Main Store',
                'asset_status' => 'Available',
            ],
            [
                'name' => 'Generators',
                'sku' => 'MAT-0332',
                'unit' => 'PCS',
                'category' => 'Fixed Asset',
                'max_stock' => 100.00,
                'reorder_level' => 0,
                'equipment_condition' => 'Good',
            ],
            [
                'name' => 'Concrete Vibrator',
                'sku' => 'MAT-0435',
                'unit' => 'PCS',
                'category' => 'Fixed Asset',
                'reorder_level' => 20,
                'equipment_condition' => 'Good',
            ],
            [
                'name' => 'Water Pump',
                'sku' => 'MAT-0627',
                'unit' => 'PCS',
                'category' => 'Fixed Asset',
                'reorder_level' => 20,
                'equipment_condition' => 'Good',
            ],
        ];

        foreach ($products as $product) {
            // updateOrCreate prevents duplicate-key errors on re-seeding
            $sku = $product['sku'];
            // Fix empty unit strings
            if (empty($product['unit'])) {
                $product['unit'] = 'PCS';
            }
            Product::updateOrCreate(['sku' => $sku], $product);
        }
    }
}
