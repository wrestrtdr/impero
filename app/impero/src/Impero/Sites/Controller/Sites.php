<?php namespace Impero\Sites\Controller;

use Impero\Apache\Record\Site;

class Sites
{

    public function postDeployAction(Site $site)
    {
        /**
         * Each site currently has same deploy procedure.
         * Future examples:
         *  - php console project:pull
         *  - git pull --ff && php some cache:clear
         *  - sh deploy.sh
         *  - ...
         */
        d('connecting');
        $connection = $site->server->getConnection();
        d('executing');
        $output = $connection->exec('cd ' . $site->getHtdocsPath() . ' && php console project:pull', $error);
        d('closing');
        $connection->close();

        d($output, $error);

        return 'cd ' . $site->getHtdocsPath() . ' && php console project:pull';
    }

}