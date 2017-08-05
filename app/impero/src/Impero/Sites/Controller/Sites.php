<?php namespace Impero\Sites\Controller;

use Impero\Apache\Record\Site;

class Sites
{

    public function postDeployAction(Site $site)
    {
        $site->server->getConnection()->exec('cd ' . $site->getHtdocsPath() . ' && php console project:pull');

        return 'deploying';
    }

}