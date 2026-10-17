<?php

use App\Enums\InventoryTransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stock ledger: one row per stock movement, in the item's base unit (+ in, - out).
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items');
            $table->tinyInteger('type')->index();
            $table->decimal('quantity', 19, 3);
            $table->decimal('balance_after', 19, 3);
            $table->decimal('unit_cost', 19, 6)->nullable();
            // No foreign key: orders can be hard-deleted and the stock history must survive that.
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->string('note', 500)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['inventory_item_id', 'id']);
        });

        // Stock already on hand starts the ledger, so every item's quantity equals the sum of its rows.
        $now = now();
        DB::table('inventory_items')
            ->where('quantity', '!=', 0)
            ->orderBy('id')
            ->each(function ($item) use ($now) {
                DB::table('inventory_transactions')->insert([
                    'inventory_item_id' => $item->id,
                    'type' => InventoryTransactionType::OPENING,
                    'quantity' => $item->quantity,
                    'balance_after' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
