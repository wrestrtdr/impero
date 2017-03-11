<?php namespace Impero\Services\Service;

class Pureftpd extends AbstractService implements ServiceInterface
{

    protected $service = 'pure-ftpd-mysql';

    protected $name = 'PureFTPd';

    public function isInstalled()
    {
        $response = $this->getConnection()
                         ->exec($this->service . ' -v');

        return strpos($response, 'No command') === false;
    }

    public function getVersion()
    {
        $response = $this->getConnection()
                         ->exec('apt-cache showpkg ' . $this->service);

        $start = strpos($response, 'Versions:') + strlen('Versions:') + 1;
        $end = strpos($response, " ", $start);
        $length = $end - $start;

        return substr($response, $start, $length);
    }

}