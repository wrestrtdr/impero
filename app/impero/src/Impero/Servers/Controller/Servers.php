<?php namespace Impero\Servers\Controller;

use Impero\Servers\Dataset\Servers as ServersDataset;
use Impero\Servers\Entity\ServersDependencies;
use Impero\Servers\Entity\ServersServices;
use Impero\Servers\Form\Server as ServerForm;
use Impero\Servers\Record\Server;
use Pckg\Generic\Service\Generic;
use Pckg\Generic\Service\Generic\CallableAction;

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

    public function getServerAction(Server $server)
    {
        return [
            'server' => $server,
        ];
    }

    public function getServerServicesAction(ServersDataset $serversDataset, $server)
    {
        return [
            'services' => $serversDataset->getServerServices(),
        ];
    }

    public function getAddServerAction(ServerForm $serverForm, Generic $genericService)
    {
        vueManager()->addView('Impero/Servers:servers/add.vue', ['serverForm' => $serverForm]);

        $genericService->touchBlock('left')
                       ->addAction(new CallableAction(function() {
                           return view('servers/add_sidebar');
                       }));

        return view('servers/add');
    }

    public function postAddServerAction(Server $server, ServerForm $serverForm)
    {
        $serverForm->populateToRecord($server);

        return response()->respondWithSuccess();
    }

    public function getRefreshServersServiceStatusAction($serversService)
    {
        $serversService = (new ServersServices())->where('id', $serversService)->oneOrFail();

        $serversService->refreshStatus();

        $serversService->withStatus();

        return response()->respondWithSuccess(['serversService' => $serversService]);
    }

    public function getRefreshServersDependencyStatusAction($serversDependency)
    {
        $serversDependency = (new ServersDependencies())->where('id', $serversDependency)->oneOrFail();

        $serversDependency->refreshStatus();

        $serversDependency->withStatus();

        return response()->respondWithSuccess(['serversDependency' => $serversDependency]);
    }

    public function getRefreshServerJobsAction(Server $server)
    {
        $server->refreshJobs();

        return response()->respondWithSuccess(['jobs' => $server->jobs]);
    }

}
