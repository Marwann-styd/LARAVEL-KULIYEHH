
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_placements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rack_level_id')
                ->constrained('rack_levels')
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->unsignedTinyInteger('pallet_quantity');

            $table->timestamps();

            // Satu tingkat hanya memiliki satu catatan penempatan aktif.
            $table->unique('rack_level_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_placements');
    }
};
