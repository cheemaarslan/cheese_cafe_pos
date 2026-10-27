<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Whether this order's recipe ingredients are taken out of inventory (InventoryDeduction), so it's done once
        // and can be undone. Null for every order placed before stock was tracked: those never touch inventory.
        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('inventory_deduction')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('inventory_deduction');
        });
    }
};
