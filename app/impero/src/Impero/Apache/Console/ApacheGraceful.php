<?php namespace Impero\Apache\Console;

use Pckg\Framework\Console\Command;

class ApacheGraceful extends Command
{

    protected function configure()
    {
        $this->setName('apache:graceful')
            ->setDescription('Graceful apache (this should be run by root privileges)');
    }

    public function handle()
    {
        if (!is_file('/tmp/impero_apache_graceful')) {
            touch('/tmp/impero_apache_graceful_not.log');

            return;
        }

        touch('/tmp/impero_apache_graceful.log');

        $this->output('Restarting apache.');
        unlink('/tmp/impero_apache_graceful');
        $this->exec([
            'apache2ctl graceful',
        ]);
        $this->output('Apache restarted');
    }

}