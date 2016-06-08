<?php namespace Impero\Impero\Provider;

use Impero\Git\Provider\Config as GitProvider;
use Impero\Impero\Controller\Impero;
use Pckg\Framework\Provider;
use Impero\Apache\Provider\Config as ApacheProvider;
use Impero\Ftp\Provider\Config as FtpProvider;
use Impero\Mysql\Provider\Config as MysqlProvider;
use Pckg\Auth\Provider\Config as AuthProvider;
use Pckg\Dynamic\Provider\Config as DynamicProvider;
use Pckg\Generic\Provider\Config as GenericProvider;
use Pckg\Manager\Provider\Config as ManagerProvider;

class Config extends Provider
{

    public function providers()
    {
        return [
            ManagerProvider::class,
            ApacheProvider::class,
            FtpProvider::class,
            MysqlProvider::class,
            GitProvider::class,
            DynamicProvider::class,
            AuthProvider::class,
            GenericProvider::class,
        ];
    }

    public function routes()
    {
        return [
            'url' => [
                '/' => [
                    'controller' => Impero::class,
                    'view'       => 'index',
                ],
            ],
        ];
    }

}