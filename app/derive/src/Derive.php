<?php

use Derive\Orders\Controller\Orders;
use Derive\Orders\Provider\Orders as OrdersProvider;
use Derive\Platform\Provider\Platform as PlatformProvider;
use Pckg\Auth\Provider\Auth as AuthProvider;
use Pckg\Concept\Reflect;
use Pckg\Framework\Provider;
use Pckg\Furs\Provider\Furs as FursProvider;
use Pckg\Generic\Middleware\EncapsulateResponse;
use Pckg\Maestro\Provider\Maestro as MaestroProvider;
use Pckg\Mail\Provider\Mail as MailProvider;
use Pckg\Manager\Provider\Manager as ManagerProvider;
use Pckg\Dynamic\Provider\Dynamic as DynamicProvider;
use Pckg\Generic\Provider\Generic as GenericProvider;

/**
 * This is core application of GNP platform. ;-)
 *
 * Class Derive
 */
class Derive extends Provider
{

    public function providers() {
        return [
            MaestroProvider::class,
            AuthProvider::class,
            MailProvider::class,
            ManagerProvider::class,
            OrdersProvider::class,
            DynamicProvider::class,
            GenericProvider::class,
            FursProvider::class,
            PlatformProvider::class,
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

}