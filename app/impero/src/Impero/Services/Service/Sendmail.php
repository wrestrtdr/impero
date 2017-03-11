<?php namespace Impero\Services\Service;

class Sendmail extends AbstractService implements ServiceInterface
{

    protected $service = 'sendmail';

    protected $name = 'Sendmail';

    public function getVersion()
    {
        $response = $this->getConnection()
                         ->exec('sendmail -d0.4 -bv root');

        $start = strpos($response, 'Version ') + strlen('Version ');
        $end = strpos($response, "\n");
        $length = $end - $start;

        return substr($response, $start, $length);
    }

}