<?php

use Impero\Apache\Migration\CreateSiteTable;
use Impero\Ftp\Migration\CreateFtpTable;
use Impero\Mysql\Migration\CreateDatabaseTable;
use Impero\Mysql\Migration\CreateUserTable;

return [
    CreateSiteTable::class,
    CreateFtpTable::class,
    CreateDatabaseTable::class,
    CreateUserTable::class,
];