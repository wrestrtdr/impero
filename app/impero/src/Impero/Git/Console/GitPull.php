<?php namespace Impero\Git\Console;

use Pckg\Framework\Console\Command;

class GitPull extends Command
{

    protected function configure() {
        $this->setName('git:pull')
             ->setDescription('Pull latest changes from GIT repository');
    }

    public function handle() {
        $this->output('Pulling changes.');

        /*$this->exec(
            [
                'git pull --ff',
            ]
        );*/

        $this->output('Changes pulled.');
    }

}