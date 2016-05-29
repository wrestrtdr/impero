<?php

return [
    'default' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'user' => 'pckg_derive',
        'pass' => 'pckg_derive',
        'db' => 'pckg_derive',
        'charset' => 'utf8',
    ],
    'queue' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'user' => 'pckg_derive',
        'pass' => 'pckg_derive',
        'db' => 'pckg_queue',
        'charset' => 'utf8',
    ],
];