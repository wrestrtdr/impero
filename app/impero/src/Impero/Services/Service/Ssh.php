<?php namespace Impero\Services\Service;

class Ssh extends AbstractService implements ServiceInterface
{

    protected $service = 'ssh';

    protected $name = 'SSH';

    public function getVersion()
    {
        $response = $this->getConnection()
                         ->exec('ssh -V');

        $length = strpos($response, ",");

        return substr($response, 0, $length);
    }

}