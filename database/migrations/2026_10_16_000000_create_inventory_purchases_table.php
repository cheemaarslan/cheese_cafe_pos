<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items');
            $table->decimal('quantity', 19, 3);
            $table->decimal('unit_cost', 19, 6)->default(0);
            $table->decimal('total_cost', 19, 6)->default(0);
            $table->date('purchase_date')->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // Stock added before purchases were logged: record what is on hand now as bought on the day
        // the item was created, so the Profit & Loss report for those days isn't missing its cost.
        $now = now();
        DB::table('inventory_items')
            ->whereNull('deleted_at')
            ->where('quantity', '>', 0)
            ->orderBy('id')
            ->each(function ($item) use ($now) {
                $unitCost = (float) ($item->unit_cost ?? 0);

                DB::table('inventory_purchases')->insert([
                    'inventory_item_id' => $item->id,
                    'quantity' => $item->quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => round((float) $item->quantity * $unitCost, 6),
                    'purchase_date' => date('Y-m-d', strtotime($item->created_at ?? $now)),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_purchases');
    }
};
