
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('transaction_code', 50)->unique();

            $table->enum('transaction_type', [
                'IN',
                'OUT',
                'MOVE'
            ]);

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->foreignId('from_rack_level_id')
                ->nullable()
                ->constrained('rack_levels')
                ->restrictOnDelete();

            $table->foreignId('to_rack_level_id')
                ->nullable()
                ->constrained('rack_levels')
                ->restrictOnDelete();

            $table->unsignedInteger('pallet_quantity');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('transaction_date');

            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['transaction_type', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_transactions');
    }
};
