<?php

use Gnp\Orders\Controller\Orders;
use Pckg\Concept\Reflect;
use Pckg\Framework\Provider;
use Pckg\Generic\Middleware\EncapsulateResponse;
use Pckg\Tempus\Console\FetchTempus;

class Tempus extends Provider
{

    public function providers() {
        return [
        ];
    }

    public function afterwares() {
        return [
            EncapsulateResponse::class,
        ];
    }

    public function routes() {
        return [
            'url' => [
                '/' => [
                    'controller' => Orders::class,
                    'view'       => 'home',
                    'name'       => 'home',
                ],
            ],
        ];
    }

    public function consoles() {
        return [
            FetchTempus::class,
        ];
    }

}