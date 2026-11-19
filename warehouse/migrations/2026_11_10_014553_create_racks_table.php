
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('racks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('area_id')
                ->constrained('areas')
                ->restrictOnDelete();

            $table->string('rack_code', 30);
            $table->unsignedTinyInteger('level_count');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['area_id', 'rack_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('racks');
    }
};
