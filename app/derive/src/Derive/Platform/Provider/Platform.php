<?php namespace Derive\Platform\Provider;

use Derive\Platform\Controller\Platform as PlatformController;
use Derive\Platform\Middleware\InitPlatformDatabase;
use Derive\Platform\Resolver\Platform as PlatformResolver;
use Pckg\Framework\Provider;

class Platform extends Provider
{

    public function routes() {
        return [
            'url' => [
                '/switch-platform/[platform]' => [
                    'controller' => PlatformController::class,
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
                '/switch-platform/[platform]' => PlatformController::class . '@switchPlatform:derive.platform.switch',
            ],
        ];
    }

    public function middlewares() {
        return [
            InitPlatformDatabase::class,
        ];
    }

}