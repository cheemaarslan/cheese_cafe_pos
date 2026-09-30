<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Full audit trail of order status transitions - who changed an order's status, from what
     * to what, and when. `orders.status` only ever holds the current value, so this table is
     * the only place the full cycle (Pending -> Accept -> ... -> Delivered) can be read back.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable('order_status_histories')) {
            Schema::create('order_status_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->unsignedTinyInteger('from_status')->nullable();
                $table->unsignedTinyInteger('to_status');
                $table->text('reason')->nullable();

                // Nullable + a denormalized name/source snapshot rather than a hard FK-only
                // reference: the account that made the change (staff or customer) may later be
                // renamed or deleted, and the history should still read sensibly either way.
                $table->unsignedBigInteger('changed_by_id')->nullable();
                $table->string('changed_by_name')->nullable();
                $table->string('changed_by_source', 20)->default('system');

                $table->timestamp('created_at')->nullable();

                $table->index(['order_id', 'created_at']);
            });
        }

        // Backfill: give every existing order a single starting history row using its current
        // status, so orders that predate this table still show something instead of an empty
        // history list. Real transitions from here on are recorded by OrderService::changeStatus().
        if (!DB::table('order_status_histories')->exists()) {
            $now = now();

            DB::table('orders')
                ->select('id', 'status', 'created_at', 'updated_at')
                ->orderBy('id')
                ->chunkById(500, function ($orders) use ($now) {
                    $rows = $orders->map(function ($order) use ($now) {
                        return [
                            'order_id'         => $order->id,
                            'from_status'      => null,
                            'to_status'        => $order->status,
                            'reason'           => null,
                            'changed_by_id'    => null,
                            'changed_by_name'  => null,
                            'changed_by_source' => 'system',
                            'created_at'       => $order->updated_at ?? $order->created_at ?? $now,
                        ];
                    })->all();

                    if ($rows) {
                        DB::table('order_status_histories')->insert($rows);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};
