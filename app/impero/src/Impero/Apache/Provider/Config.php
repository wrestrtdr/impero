<?php namespace Impero\Apache\Provider;

use Impero\Apache\Console\ApacheGraceful;
use Impero\Apache\Console\DumpVirtualhosts;
use Impero\Apache\Console\RestartApache;
use Impero\Apache\Controller\Apache;
use Impero\Apache\Record\Site;
use Impero\Apache\Record\Site\Resolver as SiteResolver;
use Pckg\Framework\Provider;
use Weblab\Generic\Middleware\EncapsulateResponse;

class Config extends Provider
{

    public function routes()
    {
        return [
            'url' => array_merge_array([
                'controller' => Apache::class,
                'afterwares' => [
                    EncapsulateResponse::class,
                ],
            ], [
                '/apache'             => [
                    'view' => 'index',
                ],
                '/apache/add'         => [
                    'view' => 'add',
                ],
                '/apache/edit/[site]' => [
                    'view'      => 'edit',
                    'resolvers' => [
                        'site' => SiteResolver::class,
                    ],
                ],
            ]),
        ];
    }

    public function consoles()
    {
        return [
            DumpVirtualhosts::class,
            RestartApache::class,
            ApacheGraceful::class,
        ];
    }

}