<?php namespace Impero\Servers\Controller;

use Impero\Servers\Dataset\Servers as ServersDataset;

class Servers
{

    public function getIndexAction()
    {
        vueManager()->addView('Impero/Servers:servers/index.vue');

        return view('servers/index');
    }

    public function getServersAction(ServersDataset $serversDataset)
    {
        return [
            'servers' => $serversDataset->getServersForUser(),
        ];
    }

    public function getViewServerAction(ServersDataset $serversDataset)
    {
        vueManager()->addView('Impero/Servers:servers/one.vue');

        return view('servers/one');
    }

    public function getServerAction(ServersDataset $serversDataset)
    {
        return [
            'server' => $serversDataset->getServersForUser()->first(),
        ];
    }

    public function getServerServicesAction(ServersDataset $serversDataset, $server)
    {
        return [
            'services' => $serversDataset->getServerServices(),
        ];
    }

}
