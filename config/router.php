<?php

return [
    'resources' => [],
    'apps'      => [
        'impero' => [
            'host' => [
                'impero.pckg-app',
                'impero.foobar.si',
                'bob.pckg.impero',
            ],
        ],
        'queue'  => [
            'host' => [
                'queue.pckg-app',
                'queue.foobar.si',
                'bob.pckg.queue',
            ],
        ],
        'derive' => [
            'host' => [
                'derive.pckg-app',
                'derive.foobar.si',
                'bob.pckg.derive',
                'bob.gonparty', // temp
            ],
        ],
        'tempus' => [
            'host' => [
                'bob.pckg.tempus',
            ],
        ],
    ],
];