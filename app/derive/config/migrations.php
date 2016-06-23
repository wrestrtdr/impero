<?php

use Gnp\Orders\Migration\OrdersTags;
use Pckg\Auth\Migration\CreateAuthTables;
use Pckg\Dynamic\Migration\CreateDynamicTables;
use Pckg\Dynamic\Migration\InstallDynamicTables;
use Pckg\Generic\Migration\CreateGenericTables;

return [
    CreateAuthTables::class,
    CreateDynamicTables::class,
    CreateGenericTables::class,
    OrdersTags::class,
    //InstallDynamicTables::class,
];
