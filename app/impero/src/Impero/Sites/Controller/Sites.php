<?php namespace Impero\Sites\Controller;

use Impero\Apache\Record\Site;

class Sites
{

    public function postExecAction(Site $site)
    {
        /**
         * Commands are sent in action post.
         */
        $commands = post('commands', []);
        d('connecting');
        $connection = $site->server->getConnection();
        d('executing');
        foreach ($commands as $command) {
            $output = null;
            $error = null;
            $output = $connection->exec('cd ' . $site->getHtdocsPath() . ' && ' . $command, $error);
            d($output, $error);
        }
        d('closing');
        $connection->close();

        return implode(' ; ', $commands);
    }

}