
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rack_levels', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rack_id')
                ->constrained('racks')
                ->restrictOnDelete();

            $table->unsignedTinyInteger('level_number');
            $table->unsignedTinyInteger('capacity_pallets')->default(3);

            $table->timestamps();

            $table->unique(['rack_id', 'level_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rack_levels');
    }
};
