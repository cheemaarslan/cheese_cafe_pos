<?php

namespace App\Enums;

interface InventoryTransactionType
{
    const OPENING      = 1;
    const PURCHASE     = 5;
    const ADJUSTMENT   = 10;
    const WASTAGE      = 15;
    const ORDER        = 20;
    const ORDER_UPDATE = 25;
    const ORDER_RETURN = 30;
    const STOCK_COUNT  = 35;
}
