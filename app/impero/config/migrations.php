<?php

use Impero\Apache\Migration\CreateSiteTable;
use Impero\Ftp\Migration\CreateFtpTable;
use Impero\Mysql\Migration\CreateDatabaseTable;
use Impero\Mysql\Migration\CreateUserTable;
use Pckg\Auth\Migration\CreateAuthTables;
use Pckg\Dynamic\Migration\CreateDynamicTables;
use Pckg\Generic\Migration\CreateGenericTables;

return [
    CreateAuthTables::class,
    CreateSiteTable::class,
    CreateFtpTable::class,
    CreateDatabaseTable::class,
    CreateUserTable::class,
    CreateDynamicTables::class,
    CreateGenericTables::class,
];