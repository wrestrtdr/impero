<?php namespace Impero\Servers\Provider;

use Impero\Servers\Controller\Servers as ServersController;
use Impero\Servers\Resolver\Server;
use Impero\Sites\Resolver\Site;
use Pckg\Framework\Provider;
use Pckg\Framework\Router\Route\Group;
use Pckg\Framework\Router\Route\Route;

class Servers extends Provider
{

    public function routes()
    {
        return [
            /**
             * Frontend routes.
             */
            (new Group([
                           'controller' => ServersController::class,
                           'urlPrefix'  => '/impero/servers',
                           'namePrefix' => 'impero.servers',
                       ]))->routes([
                                       ''                                => new Route('', 'index'),
                                       '.server'                         => (new Route('/server/[server]',
                                                                                       'viewServer'))->resolvers([
                                                                                                                     'server' => Server::class,
                                                                                                                 ]),
                                       '.addServer'                      => new Route('/add', 'addServer'),
                                       '.refreshServersServiceStatus'    => new Route('/servers-service/[serversService]/refresh',
                                                                                      'refreshServersServiceStatus'),
                                       '.refreshServersDependencyStatus' => new Route('/servers-dependency/[serversDependency]/refresh',
                                                                                      'refreshServersDependencyStatus'),
                                   ]),
            /**
             * Webhook
             */
            (new Group([
                           'controller' => ServersController::class,
                       ]))->routes([
                                       'webhook' => new Route('/webhook', 'webhook'),
                                   ]),
            /**
             * API routes.
             */
            (new Group([
                           'controller' => ServersController::class,
                           'urlPrefix'  => '/api/impero/servers',
                           'namePrefix' => 'api.impero.servers',
                       ]))->routes([
                                       ''                 => new Route('', 'servers'),
                                       '.server'          => (new Route('/[server]', 'server'))->resolvers([
                                                                                                               'server' => Server::class,
                                                                                                           ]),
                                       '.server.services' => new Route('/[server]/services', 'serverServices'),
                                   ]),
            (new Group([
                           'controller' => ServersController::class,
                           'urlPrefix'  => '/api/site',
                           'namePrefix' => 'api.impero.site',
                       ]))->routes([
                                       '.deploy' => (new Route('/[site]/deploy', 'deploy'))
                                           ->resolvers([
                                                           'site' => Site::class,
                                                       ]),
                                   ]),
        ];
    }
}