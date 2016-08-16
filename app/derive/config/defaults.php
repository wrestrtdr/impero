<?php

function makePrice($num, $dec = 2, $currency = " €")
{
    return number_format((double)$num, $dec) . $currency;
}

function cutAndMakePrice($num)
{
    $num = (float)$num;
    $decimals = (strlen(substr(strrchr((string)$num, "."), 1)) > 2) ? 2 : strlen(substr(strrchr((string)$num, "."), 1));

    return makePrice($num, $decimals);
}

function numToString($num)
{
    $num = (int)$num;

    return $num > 9999
        ? $num
        : ($num > 999
            ? "0" . $num
            : ($num > 99
                ? "00" . $num
                : ($num > 9
                    ? "000" . $num
                    : ("0000" . $num))));
}

return [
    'pckg' => [
        'auth' => [
            [
                'status'   => 'logged-out',
                'exclude'  => [
                    'login',
                    'derive.orders.voucher.preview',
                    'derive\.offers\.(.*)',
                    'derive\.basket\.(.*)',
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