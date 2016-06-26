<?php

use Gnp\Orders\Controller\Orders;
use Gnp\Orders\Provider\Config as OrdersProvider;
use Gnp\Platform\Provider\Config as PlatformProvider;
use Pckg\Auth\Provider\Config as AuthProvider;
use Pckg\Framework\Provider;
use Pckg\Furs\Provider\Config as FursProvider;
use Pckg\Generic\Middleware\EncapsulateResponse;
use Pckg\Maestro\Provider\Config as MaestroProvider;
use Pckg\Mail\Provider\Config as MailProvider;
use Pckg\Manager\Provider\Config as ManagerProvider;
use Pckg\Dynamic\Provider\Config as DynamicProvider;
use Pckg\Generic\Provider\Config as GenericProvider;

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