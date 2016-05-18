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

class Impero extends Provider
{

    public function providers()
    {
        return [
            ManagerProvider::class,
            ApacheProvider::class,
            FtpProvider::class,
            MysqlProvider::class,
            DynamicProvider::class,
            AuthProvider::class,
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

function maestro_urls($class, $slug, $record, $resolver, $alterslug = null)
{
    if (!$alterslug) {
        $alterslug = $slug;
    }

    return array_merge_array([
        'controller' => $class,
        '',
    ], [
        '/' . $alterslug                               => [
            'name' => $slug . '.list',
            'view' => 'index',
        ],
        '/' . $alterslug . '/add'                      => [
            'name' => $slug . '.add',
            'view' => 'add',
        ],
        '/' . $alterslug . '/edit/[' . $record . ']'   => [
            'name'      => $slug . '.edit',
            'view'      => 'edit',
            'resolvers' => [
                $record => $resolver,
            ],
        ],
        '/' . $alterslug . '/delete/[' . $record . ']' => [
            'name'      => $slug . '.delete',
            'view'      => 'delete',
            'resolvers' => [
                $record => $resolver,
            ],
        ],
    ]);
}