<?php

return [
    'pckg' => [
        'auth' => [
            [
                'status'   => 'logged-out',
                'exclude'  => [
                    'login',
                    'derive.orders.voucher.preview',
                    'derive\.offers\.(.*)',
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