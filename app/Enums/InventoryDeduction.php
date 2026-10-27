<?php

namespace App\Enums;

/**
 * orders.inventory_deduction: whether the order's recipe ingredients are currently out of stock (DEDUCTED) or
 * were put back after it was canceled, rejected or returned (RETURNED). Null: the order doesn't touch inventory.
 */
interface InventoryDeduction
{
    const DEDUCTED = 5;
    const RETURNED = 10;
}
