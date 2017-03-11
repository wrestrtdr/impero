<?php namespace Impero\Services\Service;

class Mysql extends AbstractService implements ServiceInterface
{

    protected $service = 'mysql';

    protected $name = 'MySQL';

    public function getVersion()
    {
        $response = $this->getConnection()
                         ->exec('mysql -V');

        $start = strpos($response, 'Ver ') + strlen('Ver ');
        $end = strpos($response, ",");
        $length = $end - $start;

        return substr($response, $start, $length);
    }

}