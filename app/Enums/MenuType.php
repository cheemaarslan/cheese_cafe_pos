<?php

namespace App\Enums;

interface MenuType
{
    const BACKEND = 1;

    const FRONTEND = 2;

    // Tabs inside the Settings section's own left-hand nav (Company, Site, Branches, ...).
    // Kept separate from BACKEND so these never leak into the main admin sidebar.
    const SETTINGS = 3;
}
