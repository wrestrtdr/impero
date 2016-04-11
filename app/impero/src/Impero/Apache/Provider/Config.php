<?php namespace Impero\Apache\Provider;

use Impero\Apache\Controller\Apache;
use Impero\Apache\Record\Site;
use Impero\Apache\Record\Site\Resolver as SiteResolver;
use Pckg\Framework\Provider;

class Config extends Provider
{

    public function routes()
    {
        return [
            'apache'             => [
                'controller' => Apache::class,
                'action'     => 'index',
            ],
            'apache/add'         => [
                'controller' => Apache::class,
                'action'     => 'add',
            ],
            'apache/edit/[site]' => [
                'controller' => Apache::class,
                'action'     => 'edit',
                'resolvers'  => [
                    'site' => SiteResolver::class,
                ],
            ],
        ];
    }

}