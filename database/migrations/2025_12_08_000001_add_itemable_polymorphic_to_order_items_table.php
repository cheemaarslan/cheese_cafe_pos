<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {

            if (!Schema::hasColumn('order_items', 'itemable_type')) {
                $table->string('itemable_type')->nullable()->after('branch_id');
            }

            if (!Schema::hasColumn('order_items', 'itemable_id')) {
                $table->unsignedBigInteger('itemable_id')->nullable()->after('itemable_type');
            }
        });

        // Only update rows where itemable_id is missing
        DB::statement('
        UPDATE order_items 
        SET itemable_type = "App\\\\Models\\\\Item",
            itemable_id = item_id
        WHERE itemable_type IS NULL
          AND item_id IS NOT NULL
    ');
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {

            if (Schema::hasColumn('order_items', 'itemable_type')) {
                $table->dropColumn('itemable_type');
            }

            if (Schema::hasColumn('order_items', 'itemable_id')) {
                $table->dropColumn('itemable_id');
            }
        });
    }

};
