<?php namespace Gnp\Platform\Console;

use Gnp\Platform\Middleware\InitPlatformDatabase;
use Pckg\Concept\Reflect;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends \Pckg\Framework\Console\Command
{

    protected function configure() {
        $this->addOptions(
            [
                'platform' => 'Platform id',
            ],
            InputOption::VALUE_REQUIRED
        );
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        Reflect::create(InitPlatformDatabase::class)->execute(function(){}, $this->option('platform'));

        parent::execute($input, $output);
    }

}