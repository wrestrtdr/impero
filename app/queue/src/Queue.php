<?php

use Impero\Apache\Provider\Config as ApacheProvider;
use Impero\Ftp\Provider\Config as FtpProvider;
use Impero\Mysql\Provider\Config as MysqlProvider;
use Pckg\Auth\Provider\Config as AuthProvider;
use Pckg\Dynamic\Provider\Config as DynamicProvider;
use Pckg\Framework\Application;
use Pckg\Framework\Provider;
use Pckg\Generic\Middleware\EncapsulateResponse;
use Pckg\Generic\Provider\Config as GenericProvider;
use Pckg\Manager\Provider\Config as ManagerProvider;
use Pckg\Queue\Provider\Queue as QueueProvider;

class Queue extends Provider
{

    public function providers()
    {
        return [
            ManagerProvider::class,
            DynamicProvider::class,
            AuthProvider::class,
            GenericProvider::class,
            QueueProvider::class,
        ];
    }

    public function afterwares()
    {
        return [
            EncapsulateResponse::class,
        ];
    }

}