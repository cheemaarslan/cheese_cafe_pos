<?php

namespace App\Enums;

/**
 * Units an inventory item can be bought and counted in. Stock itself is always kept in the base unit
 * of the group (gram, millilitre or piece) so recipes can later deduct small amounts exactly.
 */
interface InventoryUnit
{
    const KG    = 'kg';
    const G     = 'g';
    const L     = 'l';
    const ML    = 'ml';
    const PCS   = 'pcs';
    const DOZEN = 'dozen';
}
