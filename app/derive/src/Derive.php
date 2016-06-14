<?php

use Gnp\Orders\Provider\Config as OrdersProvider;
use Pckg\Framework\Provider;
use Pckg\Generic\Middleware\EncapsulateResponse;
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
            MailProvider::class,
            ManagerProvider::class,
            OrdersProvider::class,
            DynamicProvider::class,
            GenericProvider::class,
        ];
    }

    public function afterwares() {
        return [
            EncapsulateResponse::class,
        ];
    }

}