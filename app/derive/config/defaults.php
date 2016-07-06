<?php

return [
    'pckg' => [
        'auth' => [
            [
                'status'   => 'logged-out',
                'exclude'  => [
                    'login',
                    'derive.orders.voucher.preview',
                ],
                'redirect' => 'login',
            ],
            [
                'status'   => 'logged-in',
                'include'  => [
                    'login',
                ],
                'redirect' => 'home',
            ],
        ],
    ],
];