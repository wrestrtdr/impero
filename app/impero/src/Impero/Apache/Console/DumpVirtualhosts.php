<?php namespace Impero\Apache\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpVirtualhosts extends Command
{

    protected function configure()
    {
        $this->setName('apache:dumpvirtualhosts')
            ->setDescription('Dump all virtualhosts');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        die("DumpVirtualhosts::execute");
    }

}