<?php namespace Impero\Services\Service;

class Nginx extends AbstractService implements ServiceInterface
{

    protected $service = 'ngingx';

    protected $name = 'Nginx';

    public function getVersion()
    {
        $response = $this->getConnection()
                         ->exec('nginx version');

        $start = strpos($response, 'ufw ') + strlen('ufw ');
        $end = strpos($response, "\n");
        $length = $end - $start;

        return substr($response, $start, $length);
    }

}