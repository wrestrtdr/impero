<?php namespace Impero\Services\Service;

class Apache extends AbstractService implements ServiceInterface
{

    protected $service = 'apache2';

    protected $name = 'Apache';

    public function getVersion()
    {
        $response = $this->getConnection()
                         ->exec('apache2 -v');

        $start = strpos($response, 'Server version: ') + strlen('Server version: ');
        $end = strpos($response, "\n");
        $length = $end - $start;

        return substr($response, $start, $length);
    }

}