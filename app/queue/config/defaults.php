<?php

return [
    'pckg' => [
        'auth' => [
            'gates'     => [
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
            'providers' => [
                'frontend' => [
                    'type'           => \Pckg\Auth\Service\Provider\Database::class,
                    'entity'         => \Pckg\Auth\Entity\Users::class,
                    'hash'           => '', // @T00D00 - how to warn users that their passwords are "not secure"?
                    'forgotPassword' => true,
                    'userGroup'      => 'status_id',
                ],
            ],
        ],
    ],
];