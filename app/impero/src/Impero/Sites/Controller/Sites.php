<?php namespace Impero\Sites\Controller;

use Impero\Apache\Record\Site;

class Sites
{

    public function postSiteAction()
    {
        $data = only(post()->all(), ['user_id', 'server_id', 'name', 'aliases', 'ssl']);

        $site = Site::create([
                                 'server_name'   => $data['name'],
                                 'server_alias'  => $data['aliases'],
                                 'user_id'       => $data['user_id'],
                                 'error_log'     => 1,
                                 'access_log'    => 1,
                                 'created_at'    => date('Y-m-d H:i:s'),
                                 'document_root' => $data['name'],
                                 'server_id'     => $data['server_id'],
                             ]);

        $site->createOnFilesystem();

        return [
            'site' => $site,
        ];
    }

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