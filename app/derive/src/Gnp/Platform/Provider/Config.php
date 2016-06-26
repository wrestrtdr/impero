<?php namespace Gnp\Platform\Provider;

use Gnp\Platform\Controller\Platform;
use Gnp\Platform\Middleware\InitPlatformDatabase;
use Gnp\Platform\Resolver\Platform as PlatformResolver;
use Pckg\Framework\Provider;

class Config extends Provider
{

    public function routes() {
        return [
            'url' => [
                '/switch-platform/[platform]' => [
                    'controller' => Platform::class,
                    'view'       => 'switchPlatform',
                    'name'       => 'derive.platform.switch',
                    'resolvers'  => [
                        'platform' => PlatformResolver::class,
                    ],
                ],
            ],
        ];
    }

    public function middlewares() {
        return [
            InitPlatformDatabase::class
        ];
    }

}