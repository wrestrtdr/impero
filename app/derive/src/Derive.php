<?php

use Derive\Layout\Middleware\RegisterDeriveAssets;
use Derive\Offers\Provider\Offers as OffersProvider;
use Derive\Orders\Provider\Orders as OrdersProvider;
use Derive\Platform\Provider\Platform as PlatformProvider;
use Pckg\Auth\Provider\Auth as AuthProvider;
use Pckg\Concept\Reflect;
use Pckg\Dynamic\Provider\Dynamic as DynamicProvider;
use Pckg\Framework\Provider;
use Pckg\Furs\Provider\Furs as FursProvider;
use Pckg\Generic\Middleware\EncapsulateResponse;
use Pckg\Generic\Provider\Generic as GenericProvider;
use Pckg\Maestro\Provider\Maestro as MaestroProvider;
use Pckg\Mail\Provider\Mail as MailProvider;
use Pckg\Manager\Provider\Manager as ManagerProvider;

/**
 * This is core application of GNP platform. ;-)
 *
 * Class Derive
 */
class Derive extends Provider
{

    public function providers()
    {
        return [
            MaestroProvider::class,
            AuthProvider::class,
            MailProvider::class,
            ManagerProvider::class,
            OrdersProvider::class,
            OffersProvider::class,
            DynamicProvider::class,
            GenericProvider::class,
            FursProvider::class,
            PlatformProvider::class,
        ];
    }

    public function middlewares()
    {
        return [
            RegisterDeriveAssets::class,
        ];
    }

    public function afterwares()
    {
        return [
            EncapsulateResponse::class,
        ];
    }

    public function routes()
    {
        return [
            'url' => [
                /*'/v2/' => [
                    'controller' => Orders::class,
                    'view'       => 'home',
                    'name'       => 'home',
                ],*/
            ],
        ];
    }

}