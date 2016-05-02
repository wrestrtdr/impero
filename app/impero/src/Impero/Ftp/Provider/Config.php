<?php namespace Impero\Ftp\Provider;

use Impero\Apache\Record\Site;
use Impero\Ftp\Console\DumpFtpAccounts;
use Impero\Ftp\Controller\Ftp;
use Impero\Ftp\Record\Ftp\Resolver as FtpResolver;
use Pckg\Framework\Provider;

class Config extends Provider
{

    public function routes()
    {
        return [
            'url' => maestro_urls(Ftp::class, 'ftp', 'ftp', FtpResolver::class),
        ];
    }

    public function consoles()
    {
        return [
            DumpFtpAccounts::class,
        ];
    }

}