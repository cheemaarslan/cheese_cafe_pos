<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop whatever foreign key currently exists on item_id, whatever it's named -
        // don't assume Laravel's conventional "order_items_item_id_foreign" name, since
        // it may not match what's actually in the database on every environment.
        $this->dropExistingForeignKeys('order_items', 'item_id');

        // Modify column to be nullable using raw SQL
        DB::statement('ALTER TABLE `order_items` MODIFY `item_id` BIGINT UNSIGNED NULL');

        // Re-add foreign key constraint with nullable, only if one isn't already there
        if (!$this->foreignKeyExists('order_items', 'item_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign key constraint
        $this->dropExistingForeignKeys('order_items', 'item_id');

        // Modify column back to NOT NULL
        DB::statement('ALTER TABLE `order_items` MODIFY `item_id` BIGINT UNSIGNED NOT NULL');

        // Re-add foreign key constraint
        if (!$this->foreignKeyExists('order_items', 'item_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items');
            });
        }
    }

    /**
     * Drop every foreign key currently defined on $table.$column, looked up by its real
     * name in information_schema instead of assuming Laravel's default naming convention.
     * No-op if none exist, which is what makes this safe to run regardless of the
     * constraint's actual current state.
     */
    private function dropExistingForeignKeys(string $table, string $column): void
    {
        $databaseName = DB::getDatabaseName();

        $constraints = DB::select('
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ', [$databaseName, $table, $column]);

        foreach ($constraints as $constraint) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
        }
    }

    private function foreignKeyExists(string $table, string $column): bool
    {
        $databaseName = DB::getDatabaseName();

        $result = DB::selectOne('
            SELECT COUNT(*) AS total
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ', [$databaseName, $table, $column]);

        return $result && $result->total > 0;
    }
};
