<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\RebarDiaProduct;
use Illuminate\Support\Facades\DB;

class RebarProductSeeder extends Seeder
{
    public function run(): void
    {
        $diameters = RebarDiaProduct::standardDiameters();

        foreach ($diameters as $dia => $kgPerM) {
            $sku  = 'REBAR-DIA' . $dia;
            $name = 'Rebar Ø' . $dia . 'mm (Deformed Bar)';

            // Find or create the product
            $product = Product::withTrashed()->where('sku', $sku)->first();

            if (!$product) {
                $product = Product::create([
                    'name'            => $name,
                    'sku'             => $sku,
                    'unit'            => 'kg',
                    'category'        => 'Consumable',
                    'sub_category'    => 'Rebar',
                    'standard_length' => 12.00,      // 12m standard bar
                    'unit_price'      => 0.00,
                    'selling_price'   => 0.00,
                    'max_stock'       => 0,
                    'reorder_level'   => 0,
                ]);
            } elseif ($product->trashed()) {
                $product->restore();
            }

            // Upsert the rebar_dia_products record
            RebarDiaProduct::updateOrCreate(
                ['diameter' => $dia],
                [
                    'unit_weight_kg_per_m' => $kgPerM,
                    'product_id'           => $product->id,
                    'standard_length_m'    => '12',
                ]
            );
        }
    }
}
