<?php namespace Impero\Apache\Console;

use Impero\Apache\Entity\Sites;
use Impero\Apache\Record\Site;
use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpVirtualhosts extends Command
{

    protected function configure()
    {
        $this->setName('apache:dump')
             ->setDescription('Dump all virtualhosts');
    }

    public function handle(Sites $sites)
    {
        $this->output('Building virtualhosts');
        $sites = $sites->all();
        $virtualhosts = [];
        $sites->each(
            function(Site $site) use (&$virtualhosts) {
                $virtualhosts[] = $site->getVirtualhost();
            }
        );

        $virtualhosts = implode("\n\n", $virtualhosts);

        $this->output('Dumping virtualhosts');

        file_put_contents(path('storage') . 'impero' . path('ds') . 'virtualhosts.conf', $virtualhosts);

        $this->output('Virtualhosts were dumped, waiting for apache graceful');
    }

}