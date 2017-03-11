<?php namespace Impero\Services\Service;

class AbstractService
{

    /**
     * @var SshConnection
     */
    protected $connection;

    protected $service;

    protected $name;

    public function getName()
    {
        return $this->name;
    }

    public function __construct(SshConnection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function isInstalled()
    {
        $response = $this->getConnection()
                         ->exec('service ' . $this->service . ' status');

        $notFound = strpos($response, 'Loaded: not-found');
        $loaded = strpos($response, 'Loaded: loaded');

        return $loaded && !$notFound;
    }

    public function getStatus()
    {
        $response = $this->getConnection()
                         ->exec('service ' . $this->service . ' status');

        $loaded = strpos($response, 'Loaded: loaded');
        $active = strpos($response, 'Active: active (running)');
        $exited = strpos($response, 'Active: active (exited)');
        $notFound = strpos($response, 'Loaded: not-found');

        return $loaded
            ? ($active
                ? 'ok'
                : ($exited
                    ? 'ok, exited'
                    : 'inactive'))
            : ($notFound
                ? 'missing'
                : 'error');
    }

}