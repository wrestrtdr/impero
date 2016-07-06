<?php

return [
    'pckg' => [
        'auth' => [
            [
                'status'   => 'logged-out',
                'exclude'  => [
                    'login',
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