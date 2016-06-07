<?php namespace Impero\Apache\Console;

use Pckg\Framework\Console\Command;

class LetsEncryptRenew extends Command
{

    protected function configure() {
        $this->setName('apache:letsencryptrenew')
             ->setDescription('Renew LetsEncrypt certificates');
    }

    /**
     * List all sites that need to be renewed.
     */
    public function handle() {
        
    }

}