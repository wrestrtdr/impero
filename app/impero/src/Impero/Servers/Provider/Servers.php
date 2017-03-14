<?php namespace Impero\Servers\Provider;

use Impero\Servers\Controller\Servers as ServersController;
use Pckg\Framework\Provider;
use Pckg\Framework\Router\Route\Group;
use Pckg\Framework\Router\Route\Route;

class Servers extends Provider
{

    public function routes()
    {
        return [
            (new Group([
                           'controller' => ServersController::class,
                           'urlPrefix'  => '/impero/servers',
                           'namePrefix' => 'impero.servers',
                       ]))->routes([
                                       ''                             => new Route('', 'index'),
                                       '.server'                      => new Route('/server/[server]', 'viewServer'),
                                       '.addServer'                   => new Route('/add', 'addServer'),
                                       '.refreshServersServiceStatus' => new Route('/servers-service/[serversService]/refresh',
                                                                                   'refreshServersServiceStatus'),
                                       '.refreshServersDependencyStatus' => new Route('/servers-dependency/[serversDependency]/refresh',
                                                                                   'refreshServersDependencyStatus'),
                                   ]),
            (new Group([
                           'controller' => ServersController::class,
                           'urlPrefix'  => '/api/impero/servers',
                           'namePrefix' => 'api.impero.servers',
                       ]))->routes([
                                       ''                 => new Route('', 'servers'),
                                       '.server'          => new Route('/[server]', 'server'),
                                       '.server.services' => new Route('/[server]/services', 'serverServices'),
                                   ]),
        ];
    }
}