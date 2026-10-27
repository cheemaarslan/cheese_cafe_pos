<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A physical stock count: the stock actually found replaces the system's, and each difference is a
        // STOCK_COUNT ledger entry. The lines keep what the system expected, so the variance can be reviewed.
        Schema::create('inventory_counts', function (Blueprint $table) {
            $table->id();
            $table->string('note', 500)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_count_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_count_id')->constrained('inventory_counts')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items');
            // All in the inventory item's base unit (g, ml or pcs); difference = counted - system
            $table->decimal('system_quantity', 19, 3);
            $table->decimal('counted_quantity', 19, 3);
            $table->decimal('difference', 19, 3);
            $table->decimal('unit_cost', 19, 6)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_count_items');
        Schema::dropIfExists('inventory_counts');
    }
};
