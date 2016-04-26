<?php namespace Impero\Apache\Provider;

use Impero\Apache\Console\ApacheGraceful;
use Impero\Apache\Console\DumpVirtualhosts;
use Impero\Apache\Console\RestartApache;
use Impero\Apache\Controller\Apache;
use Impero\Apache\Record\Site;
use Impero\Apache\Record\Site\Resolver as SiteResolver;
use Pckg\Framework\Provider;

class Config extends Provider
{

    public function routes()
    {
        return [
            'url' => array_merge_array([
                'controller' => Apache::class,
            ], [
                '/apache'               => [
                    'name' => 'apache.list',
                    'view' => 'index',
                ],
                '/apache/add'           => [
                    'view' => 'add',
                ],
                '/apache/edit/[site]'   => [
                    'name'      => 'apache.edit',
                    'view'      => 'edit',
                    'resolvers' => [
                        'site' => SiteResolver::class,
                    ],
                ],
                '/apache/delete/[site]' => [
                    'name'      => 'apache.delete',
                    'view'      => 'delete',
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