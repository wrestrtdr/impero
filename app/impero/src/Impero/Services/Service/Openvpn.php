<?php namespace Impero\Services\Service;

class Openvpn extends AbstractService implements ServiceInterface
{

    protected $service = 'openvpn';

    protected $name = 'OpenVPN';

    public function getVersion()
    {
        $response = $this->getConnection()
                         ->exec('openvnp --version');

        return $response;
        $start = strpos($response, 'OpenVPN ') + strlen('OpenVPN ');
        $end = strpos($response, " ", $start);
        $length = $end - $start;

        return substr($response, $start, $length);
    }

}