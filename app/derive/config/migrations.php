<?php

use Pckg\Auth\Migration\CreateAuthTables;
use Pckg\Dynamic\Migration\CreateDynamicTables;
use Pckg\Generic\Migration\CreateGenericTables;

return [
    CreateAuthTables::class,
    CreateDynamicTables::class,
    CreateGenericTables::class,
];
