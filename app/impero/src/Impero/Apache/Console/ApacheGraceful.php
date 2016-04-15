<?php namespace Impero\Apache\Console;

use Pckg\Framework\Console\Command;

class ApacheGraceful extends Command
{

    protected function configure()
    {
        $this->setName('apache:graceful')
            ->setDescription('Graceful apache, this should be run by root');
    }

    public function handle()
    {
        if (!is_file('/tmp/impero_restart_apache')) {
            touch('/tmp/impero_restart_apache.log');
            return;
        }

        $this->output('Restarting apache.');
        unlink('/tmp/impero_restart_apache');
        $this->exec([
            'apache2ctl graceful',
        ]);
        $this->output('Apache restarted');
    }

}