<?php namespace Impero\Services\Service;

class Php extends AbstractService implements ServiceInterface
{

    protected $service = 'php';

    protected $name = 'PHP';

    public function isInstalled()
    {
        $response = $this->getConnection()
                         ->exec($this->service . ' -v');

        return strpos($response, 'No command') === false;
    }

    public function getVersion()
    {
        $response = $this->getConnection()
                         ->exec($this->service . ' -v');

        $start = strpos($response, 'PHP ') + strlen('PHP ');
        $end = strpos($response, " ", $start);
        $length = $end - $start;

        return substr($response, $start, $length);
    }

    public function getStatus()
    {
        return 'ok';
    }

}