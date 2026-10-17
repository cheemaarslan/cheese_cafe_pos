<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Stock is now kept in a base unit (g, ml or pcs) and `purchase_unit` is the unit the admin enters and
 * sees it in. Items saved before this had a free-text unit, so each is converted: 10 kg becomes 10000 g
 * with purchase_unit kg (still shown as 10 kg), and its unit cost and logged purchases are scaled to
 * match so stock value and the Profit & Loss totals stay exactly the same.
 */
return new class extends Migration
{
    // Spellings found in the old free-text unit (lowercase, letters only) => [purchase unit, base unit, factor]
    private const KNOWN_UNITS = [
        'kg' => ['kg', 'g', 1000], 'kgs' => ['kg', 'g', 1000], 'kilo' => ['kg', 'g', 1000],
        'kilos' => ['kg', 'g', 1000], 'kilogram' => ['kg', 'g', 1000], 'kilograms' => ['kg', 'g', 1000],
        'kilogramme' => ['kg', 'g', 1000], 'kilogrammes' => ['kg', 'g', 1000],

        'g' => ['g', 'g', 1], 'gm' => ['g', 'g', 1], 'gms' => ['g', 'g', 1], 'gr' => ['g', 'g', 1],
        'grm' => ['g', 'g', 1], 'grms' => ['g', 'g', 1], 'gram' => ['g', 'g', 1], 'grams' => ['g', 'g', 1],
        'gramme' => ['g', 'g', 1], 'grammes' => ['g', 'g', 1],

        'l' => ['l', 'ml', 1000], 'lt' => ['l', 'ml', 1000], 'ltr' => ['l', 'ml', 1000],
        'ltrs' => ['l', 'ml', 1000], 'litr' => ['l', 'ml', 1000], 'liter' => ['l', 'ml', 1000],
        'liters' => ['l', 'ml', 1000], 'litre' => ['l', 'ml', 1000], 'litres' => ['l', 'ml', 1000],

        'ml' => ['ml', 'ml', 1], 'mls' => ['ml', 'ml', 1], 'milliliter' => ['ml', 'ml', 1],
        'milliliters' => ['ml', 'ml', 1], 'millilitre' => ['ml', 'ml', 1], 'millilitres' => ['ml', 'ml', 1],

        'dozen' => ['dozen', 'pcs', 12], 'dozens' => ['dozen', 'pcs', 12], 'dz' => ['dozen', 'pcs', 12],
        'doz' => ['dozen', 'pcs', 12],

        'pc' => ['pcs', 'pcs', 1], 'pcs' => ['pcs', 'pcs', 1], 'piece' => ['pcs', 'pcs', 1],
        'pieces' => ['pcs', 'pcs', 1], 'nos' => ['pcs', 'pcs', 1], 'no' => ['pcs', 'pcs', 1],
        'unit' => ['pcs', 'pcs', 1], 'units' => ['pcs', 'pcs', 1], 'qty' => ['pcs', 'pcs', 1],
        'each' => ['pcs', 'pcs', 1], 'ea' => ['pcs', 'pcs', 1],
    ];

    private const FACTORS = ['kg' => 1000, 'g' => 1, 'l' => 1000, 'ml' => 1, 'pcs' => 1, 'dozen' => 12];

    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('purchase_unit', 20)->default('pcs')->after('unit');
        });

        DB::transaction(function () {
            DB::table('inventory_items')->orderBy('id')->each(function ($item) {
                $key = preg_replace('/[^a-z]/', '', strtolower((string) $item->unit));
                $known = self::KNOWN_UNITS[$key] ?? null;
                [$purchaseUnit, $baseUnit, $factor] = $known ?? ['pcs', 'pcs', 1];

                $changes = [
                    'unit' => $baseUnit,
                    'purchase_unit' => $purchaseUnit,
                    'quantity' => round((float) $item->quantity * $factor, 3),
                    'minimum_stock' => round((float) $item->minimum_stock * $factor, 3),
                    'unit_cost' => $item->unit_cost === null ? null : round((float) $item->unit_cost / $factor, 6),
                ];

                // A unit like "bottle" or "packet" is counted as pieces; keep the old name so it isn't lost.
                if (! $known && trim((string) $item->unit) !== '') {
                    $changes['notes'] = trim(trim((string) $item->notes) . "\nUnit before update: " . trim($item->unit));
                }

                DB::table('inventory_items')->where('id', $item->id)->update($changes);

                if ($factor !== 1 && Schema::hasTable('inventory_purchases')) {
                    DB::table('inventory_purchases')->where('inventory_item_id', $item->id)->update([
                        'quantity' => DB::raw("ROUND(quantity * {$factor}, 3)"),
                        'unit_cost' => DB::raw("ROUND(unit_cost / {$factor}, 6)"),
                    ]);
                }
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::table('inventory_items')->orderBy('id')->each(function ($item) {
                $factor = self::FACTORS[$item->purchase_unit] ?? 1;

                DB::table('inventory_items')->where('id', $item->id)->update([
                    'unit' => $item->purchase_unit,
                    'quantity' => round((float) $item->quantity / $factor, 3),
                    'minimum_stock' => round((float) $item->minimum_stock / $factor, 3),
                    'unit_cost' => $item->unit_cost === null ? null : round((float) $item->unit_cost * $factor, 6),
                ]);

                if ($factor !== 1 && Schema::hasTable('inventory_purchases')) {
                    DB::table('inventory_purchases')->where('inventory_item_id', $item->id)->update([
                        'quantity' => DB::raw("ROUND(quantity / {$factor}, 3)"),
                        'unit_cost' => DB::raw("ROUND(unit_cost * {$factor}, 6)"),
                    ]);
                }
            });
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn('purchase_unit');
        });
    }
};
