<?php

use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->nullable()->index();
            $table->string('category')->nullable()->index();
            $table->decimal('quantity', 19, 3)->default(0);
            $table->string('unit', 50);
            $table->decimal('minimum_stock', 19, 3)->default(0);
            $table->decimal('unit_cost', 19, 6)->nullable();
            $table->string('supplier')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(Status::ACTIVE);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'quantity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
