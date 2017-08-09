<?php namespace Impero\Servers\Controller;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Impero\Servers\Dataset\Servers as ServersDataset;
use Impero\Servers\Entity\ServersDependencies;
use Impero\Servers\Entity\ServersServices;
use Impero\Servers\Entity\Systems;
use Impero\Servers\Form\Server as ServerForm;
use Impero\Servers\Record\Server;
use Impero\Services\Service\SshConnection;
use Pckg\Generic\Service\Generic;
use Pckg\Generic\Service\Generic\CallableAction;
use Throwable;

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

    public function getWebhookAction()
    {
        /**
         * Hardcoded, currently used for gnpdev.
         */
        //$server = (new \Impero\Servers\Entity\Servers())->where('id', 2)->one();
        //$server->getConnection()->exec('cd /www/gnpdev/gnpdev.gonparty.eu/htdocs/ && php console project:pull');

        return 'ok';
    }

    public function postWebhookAction()
    {
        return $this->getWebhookAction();
    }

    public function postInstallNewServerAction()
    {
        /**
         * Get encrypted password and decrypt it.
         */
        $encryptedPassword = post('password', null);
        $password = Crypto::decrypt($encryptedPassword, Key::loadFromAsciiSafeString(config('security.key')));
        $hostname = post('hostname');
        $ip = server('REMOTE_ADDR', null);
        $port = post('port', 22);
        $user = 'impero';
        d("pass", $password);

        /**
         * Create new server.
         */
        $server = Server::create([
                                     'system_id' => Systems::OS_UBUNTU_1604_LTS_X64,
                                     'status'    => 'new',
                                     'name'      => $hostname,
                                     'ip'        => $ip,
                                     'ptr'       => $hostname,
                                     'port'      => $port,
                                     'user'      => $user,
                                 ]);

        /**
         * We will generate ssh key for local www-data user to connect to server with impero username.
         */
        $privateKey = path('storage') . 'private' . path('ds') . 'keys' . path('ds') . 'id_rsa_' . $server->id;
        $output = $return_var = null;
        $command = 'ssh-keygen -b 4096 -t rsa -C \'' . $user . '@' . $ip . '\' -f ' . $privateKey . ' -N "" 2>&1';
        exec($command, $output, $return_var);
        d("generated", $command, $output, $return_var);

        /**
         * Change permissions.
         */
        chown($privateKey, $user);

        /**
         * Then we will transfer key to remote.
         * If this fails (firewall), notify user.
         */
        $output = $return_var = null;
        $command = 'sshpass -p ' . $password . ' ssh-copy-id -p ' . $port . ' -i ' . $privateKey . '.pub ' . $user . '@' . $ip . ' 2>&1';
        exec($command, $output, $return_var);
        d("copied", $command, $output, $return_var);

        /**
         * Check if transfer was successful.
         * If successful, disable login with password and change ssh config
         * # PermitRootLogin no / without-password
         */
        try {
            $connection = new SshConnection($ip, $user, $port, $privateKey);
        } catch (Throwable $e) {
            dd('error', exception($e));
            return response()->respondWithError([
                                                    'error' => exception($e),
                                                ]);
        }

        dd('success');

        return response()->respondWithSuccess();
        /**
         * chmod -R g+w
         */
    }

    public function getInstallShAction()
    {
        /**
         * Generate password.
         */
        $password = auth()->createPassword(40);

        /**
         * Encrypt it for useradd action.
         */
        $allowed = "abcdefghiklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ0123456789";
        $salt = substr($allowed, rand(0, strlen($allowed) - 1), 1) . substr($allowed, rand(0, strlen($allowed) - 1), 1);
        $cryptedPassword = crypt($password, $salt);

        /**
         * Encrypt it so we can decrypt it later and connect to server.
         */
        $secret = Crypto::encrypt($password, Key::loadFromAsciiSafeString(config('security.key')));

        return view('servers/install.sh', [
            'password'        => $password,
            'cryptedPassword' => $cryptedPassword,
            'secret'          => $secret,
            'userhash'        => sha1(1),
        ]);
    }

}
