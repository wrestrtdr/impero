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
        $output = $site->server->getConnection()
                     ->exec('cd ' . $site->getHtdocsPath() . ' && php console project:pull');

        dd($output);

        return 'cd ' . $site->getHtdocsPath() . ' && php console project:pull' . "<br/>\n" . $output;
    }

}