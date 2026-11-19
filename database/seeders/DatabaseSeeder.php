<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            WarehouseSeeder::class,
            AreaSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            RackSeeder::class,
            RackLevelSeeder::class,
            UserSeeder::class,
        ]);
    }
}