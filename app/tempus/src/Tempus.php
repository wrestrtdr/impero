<?php

use Pckg\Concept\Reflect;
use Pckg\Framework\Provider;
use Pckg\Generic\Middleware\EncapsulateResponse;
use Pckg\Generic\Provider\Generic as GenericProvider;
use Pckg\Manager\Provider\Manager as ManagerProvider;
use Pckg\Dynamic\Provider\Dynamic as DynamicProvider;
use Pckg\Tempus\Console\FetchTempus;
use Pckg\Tempus\Controller\Tempus as TempusController;

class Tempus extends Provider
{

    public function providers() {
        return [
            DynamicProvider::class,
            GenericProvider::class,
            ManagerProvider::class,
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
                    'controller' => TempusController::class,
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