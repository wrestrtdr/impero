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

    /**
     * @T00D00 ... this and newRoutes() methods need to be added to base Provider for better readability of app.
     * @return array
     */
    public function routeResolvers() {
        return [
            'platform' => PlatformResolver::class,
        ];
    }

    public function newRoutes() {
        return [
            'url' => [
                '/switch-platform/[platform]' => Platform::class . '@switchPlatform:derive.platform.switch',
            ],
        ];
    }

    public function middlewares() {
        return [
            InitPlatformDatabase::class,
        ];
    }

}