<?php

use Pckg\Auth\Migration\CreateAuthTables;
use Pckg\Queue\Migration\Queue as CreateQueueTable;

return [
    CreateAuthTables::class,
    CreateQueueTable::class,
];