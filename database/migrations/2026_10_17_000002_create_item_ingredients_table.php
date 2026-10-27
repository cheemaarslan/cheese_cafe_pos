<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Recipe lines of menu items. A line with neither a variation nor an extra is used by every order of the
        // item; one with a variation (e.g. size Large) only when that variation is chosen, on top of the
        // every-order lines; one with an extra only when that extra is added.
        Schema::create('item_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('item_variation_id')->nullable()->constrained('item_variations')->cascadeOnDelete();
            $table->foreignId('item_extra_id')->nullable()->constrained('item_extras')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items');
            // In the inventory item's base unit (g, ml or pcs), for one of the menu item.
            $table->decimal('quantity', 19, 3);
            $table->timestamps();

            $table->index(['item_id', 'item_variation_id', 'item_extra_id'], 'item_ingredients_item_part_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_ingredients');
    }
};
