<?php namespace Impero\Apache\Console;

use Pckg\Framework\Console\Command;

class RestartApache extends Command
{

    protected function configure()
    {
        $this->setName('apache:restart')
            ->setDescription('Restart apache via scheduler');
    }

    public function handle()
    {
        $this->output('Requesting apache restart.');
        touch('/tmp/impero_apache_graceful');
        $this->output('Apache restart requested.');
    }

}