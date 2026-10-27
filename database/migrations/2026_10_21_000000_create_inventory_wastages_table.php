<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stock thrown away (spoiled, dropped, burnt...). Each row's stock removal is a WASTAGE ledger entry;
        // deleting a row puts the stock back with a second WASTAGE entry, so the ledger is never rewritten.
        Schema::create('inventory_wastages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items');
            // In the inventory item's base unit (g, ml or pcs)
            $table->decimal('quantity', 19, 3);
            $table->decimal('unit_cost', 19, 6)->nullable();
            $table->tinyInteger('reason');
            $table->string('note', 500)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_wastages');
    }
};
