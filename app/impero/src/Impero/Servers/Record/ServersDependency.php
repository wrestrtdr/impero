<?php namespace Impero\Servers\Record;

use Impero\Servers\Entity\ServersServices;
use Pckg\Database\Record;

class ServersDependency extends Record
{

    protected $entity = ServersServices::class;

    protected $toArray = ['status'];

    public function refreshStatus()
    {
        $dependency = $this->dependency;
        $connection = $this->server->getConnection();
        $handler = $dependency->getHandler($connection);

        /**
         * Fetch status.
         */
        $isInstalled = $handler->isInstalled();
        $version = null;
        $status = null;
        if (!$isInstalled) {
            $this->status_id = 'missing';
            $this->save();

            return;
        }

        $status = $handler->getStatus();
        $version = $handler->getVersion();
        $this->setAndSave([
                              'status_id' => $status,
                              'version'   => $version,
                          ]);
    }

}