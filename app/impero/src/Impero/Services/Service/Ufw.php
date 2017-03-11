<?php namespace Impero\Services\Service;

class Ufw extends AbstractService implements ServiceInterface
{

    protected $service = 'ufw';

    protected $name = 'UFW';

    public function getVersion()
    {
        $response = $this->getConnection()
                         ->exec('ufw version');

        $start = strpos($response, 'ufw ') + strlen('ufw ');
        $end = strpos($response, "\n");
        $length = $end - $start;

        return substr($response, $start, $length);
    }

}