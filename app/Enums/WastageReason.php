<?php

namespace App\Enums;

interface WastageReason
{
    const SPOILED       = 5;
    const DAMAGED       = 10;
    const KITCHEN_ERROR = 15;
    const OTHER         = 20;
}
