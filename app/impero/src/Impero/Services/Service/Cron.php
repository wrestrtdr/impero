<?php namespace Impero\Services\Service;

class Cron extends AbstractService implements ServiceInterface
{

    protected $service = 'cron';

    protected $name = 'Cron';

    public function getVersion()
    {
        return null;
    }

}