<?php

namespace App\Libraries;

use App\Enums\InventoryUnit;

/**
 * Converts between the unit an inventory item is bought in (kg, litre, dozen...) and the base unit its
 * stock is kept in (g, ml, pcs).
 */
class InventoryUnitLibrary
{
    // unit => [base unit, how many base units one of it is]
    private const UNITS = [
        InventoryUnit::KG    => [InventoryUnit::G, 1000],
        InventoryUnit::G     => [InventoryUnit::G, 1],
        InventoryUnit::L     => [InventoryUnit::ML, 1000],
        InventoryUnit::ML    => [InventoryUnit::ML, 1],
        InventoryUnit::PCS   => [InventoryUnit::PCS, 1],
        InventoryUnit::DOZEN => [InventoryUnit::PCS, 12],
    ];

    private const LABELS = [
        InventoryUnit::KG    => 'kg',
        InventoryUnit::G     => 'g',
        InventoryUnit::L     => 'L',
        InventoryUnit::ML    => 'ml',
        InventoryUnit::PCS   => 'pcs',
        InventoryUnit::DOZEN => 'dozen',
    ];

    public static function units(): array
    {
        return array_keys(self::UNITS);
    }

    public static function baseUnit(?string $unit): string
    {
        return self::UNITS[$unit][0] ?? InventoryUnit::PCS;
    }

    public static function factor(?string $unit): int
    {
        return self::UNITS[$unit][1] ?? 1;
    }

    public static function label(?string $unit): string
    {
        return self::LABELS[$unit] ?? (string) $unit;
    }

    /**
     * A quantity entered in $unit (e.g. 2.5 kg), in its base unit (2500 g).
     */
    public static function toBase($quantity, ?string $unit): float
    {
        return round((float) $quantity * self::factor($unit), 3);
    }

    /**
     * A quantity in the base unit (2500 g), in $unit (2.5 kg).
     */
    public static function fromBase($quantity, ?string $unit): float
    {
        return round((float) $quantity / self::factor($unit), 6);
    }

    /**
     * A price per $unit (Rs 1200 per kg), per base unit (Rs 1.2 per g).
     */
    public static function costToBase($cost, ?string $unit): float
    {
        return round((float) $cost / self::factor($unit), 6);
    }

    /**
     * A price per base unit (Rs 1.2 per g), per $unit (Rs 1200 per kg).
     */
    public static function costFromBase($cost, ?string $unit): float
    {
        return round((float) $cost * self::factor($unit), 4);
    }
}
