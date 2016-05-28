<?php

return [
    'default' => [
        'driver'  => 'mysql',
        'host'    => '127.0.0.1',
        'user'    => 'pckg_impero',
        'pass'    => 'pckg_impero',
        'db'      => 'pckg_impero',
        'charset' => 'utf8',
    ],
    'queue'   => [
        'driver'  => 'mysql',
        'host'    => '127.0.0.1',
        'user'    => 'pckg_queue',
        'pass'    => 'pckg_queue',
        'db'      => 'pckg_queue',
        'charset' => 'utf8',
    ],
    /*    'faker' => [
            'driver'  => 'faker',
            'host'    => 'oxygenium.schtr4jh.net',
            'user'    => 'pckg_impero',
            'pass'    => 'pckg_impero',
            'db'      => 'pckg_impero',
            'charset' => 'utf8',
        ],*/
];
