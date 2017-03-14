<?php namespace Impero\Servers\Record;

use Impero\Servers\Entity\Servers;
use Impero\Services\Service\SshConnection;
use Pckg\Database\Record;

class Server extends Record
{

    protected $entity = Servers::class;

    protected $toArray = ['services', 'dependencies'];

    public function getConnection()
    {
        return new SshConnection();
    }

}