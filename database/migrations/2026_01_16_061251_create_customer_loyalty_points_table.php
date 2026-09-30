<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('customer_loyalty_points')) {
            return;
        }

        Schema::create('customer_loyalty_points', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('customer_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('order_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Snapshot data (VERY IMPORTANT)
            $table->decimal('order_subtotal', 10, 2);
            $table->decimal('percentage', 5, 2);

            // Calculated points
            $table->integer('points');

            // Status (active / inactive / expired / reversed)
            $table->tinyInteger('status')->default(5)
                  ->comment('5=active, 10=inactive');

            // Optional audit fields
            $table->timestamp('earned_at')->nullable();
            $table->timestamp('expired_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['customer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_loyalty_points');
    }
};