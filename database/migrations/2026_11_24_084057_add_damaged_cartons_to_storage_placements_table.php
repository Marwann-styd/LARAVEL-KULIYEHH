<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('storage_placements', function (Blueprint $table) {
            $table->unsignedInteger('damaged_cartons')->default(0)->after('pallet_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('storage_placements', function (Blueprint $table) {
            $table->dropColumn('damaged_cartons');
        });
    }
};
