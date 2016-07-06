<?php

return [
    'pckg' => [
        'auth' => [
            [
                'status'   => 'logged-out',
                'exclude'  => [
                    'login',
                    'impero.git.webhook',
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