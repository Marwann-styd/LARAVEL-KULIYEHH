<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = Warehouse::all();

        foreach ($warehouses as $warehouse) {
            Area::updateOrCreate(
                [
                    'warehouse_id' => $warehouse->id,
                    'area_code' => 'MAIN-01',
                ],
                [
                    'area_name' => 'Area Utama',
                    'description' => 'Area utama penyimpanan produk Finished Goods.',
                    'is_active' => true,
                ]
            );
        }
    }
}
