<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damaged_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('rack_level_id')->constrained('rack_levels')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('reported_by')->constrained('users')->restrictOnDelete();

            $table->unsignedInteger('damaged_cartons'); // Jumlah dus/karton mie instan yang rusak/bocor
            $table->string('damage_category', 80); // Contoh: Dus Penyok/Sobek, Kemasan Bocor, Basah/Lembab
            $table->string('photo_path'); // Lokasi file foto bukti jepretan kamera
            $table->text('notes')->nullable();
            $table->timestamp('reported_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damaged_stocks');
    }
};
