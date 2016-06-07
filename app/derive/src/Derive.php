<?php

use Pckg\Framework\Provider;
use Pckg\Generic\Middleware\EncapsulateResponse;
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
            ManagerProvider::class,
            \Gnp\Orders\Provider\Config::class,
            DynamicProvider::class,
            GenericProvider::class,
        ];
    }

    public function afterwares()
    {
        return [
            EncapsulateResponse::class,
        ];
    }

}