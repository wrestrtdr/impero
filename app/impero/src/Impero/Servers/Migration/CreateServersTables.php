<?php namespace Impero\Servers\Migration;

use Pckg\Migration\Migration;

class CreateServersTables extends Migration
{

    public function up()
    {
        /**
         * Servers.
         */
        $servers = $this->table('servers');
        $servers->integer('system_id')->references('systems');
        $servers->varchar('status')->references('list_items', 'slug');
        $servers->varchar('name');
        $servers->varchar('ip');
        $servers->varchar('ptr');

        /**
         * Services
         */
        $serversServices = $this->table('servers_services');
        $serversServices->integer('server_id')->references('servers');
        $serversServices->integer('service_id')->references('services');
        $serversServices->varchar('status')->references('list_items', 'slug');
        $serversServices->varchar('version');

        $services = $this->table('services');
        $services->varchar('name');
        $services->varchar('service');

        /**
         * Dependencies
         */
        $dependencies = $this->table('dependencies');
        $dependencies->varchar('name');
        $dependencies->varchar('dependency');

        $serversDependencies = $this->table('servers_dependencies');
        $serversDependencies->integer('server_id')->references('servers');
        $serversDependencies->integer('dependency_id')->references('dependencies');
        $serversDependencies->varchar('status')->references('list_items', 'slug');
        $serversDependencies->varchar('version');

        /**
         * Websites - sites?
         */
        /**
         * Jobs
         */
        $jobs = $this->table('jobs');
        $jobs->integer('server_id')->references('servers');
        $jobs->varchar('name');
        $jobs->text('command');
        $jobs->varchar('frequency');
        $jobs->varchar('status')->references('list_items', 'slug');

        /**
         * Firewalls
         */
        $firewalls = $this->table('firewalls');
        $firewalls->integer('server_id')->references('servers');
        $firewalls->varchar('rule')->references('list_items', 'slug');
        $firewalls->varchar('from');
        $firewalls->varchar('port');
        $firewalls->varchar('direction')->references('list_items', 'slug');

        /**
         * Logs
         */
        $serverLogs = $this->table('server_logs');
        $serverLogs->integer('server_id')->references('servers');
        $serverLogs->datetime('created_at');
        $serverLogs->varchar('type'); // morph - service, server, deployment, application, ...
        $serverLogs->integer('poly_id');

        /**
         * Operating systems.
         */
        $systems = $this->table('systems');
        $systems->slug();
        $systems->varchar('name');

        /**
         * Tags
         */
        $tags = $this->table('tags');
        $tags->integer('server_id')->references('servers');
        $tags->varchar('tag');

        $this->save();
    }

}