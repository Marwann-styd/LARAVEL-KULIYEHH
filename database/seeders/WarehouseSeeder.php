<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        Warehouse::updateOrCreate(
            ['warehouse_code' => 'FG-01'],
            [
                'warehouse_name' => 'Warehouse Finished Goods 1',
                'description' => 'Lokasi gudang Finished Goods pertama.',
                'is_active' => true,
            ]
        );

        Warehouse::updateOrCreate(
            ['warehouse_code' => 'FG-02'],
            [
                'warehouse_name' => 'Warehouse Finished Goods 2',
                'description' => 'Lokasi gudang Finished Goods kedua.',
                'is_active' => true,
            ]
        );
    }
}
