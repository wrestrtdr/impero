<?php namespace Impero\Apache\Provider;

use Impero\Apache\Console\ApacheGraceful;
use Impero\Apache\Console\DumpVirtualhosts;
use Impero\Apache\Console\LetsEncryptRenew;
use Impero\Apache\Console\RestartApache;
use Impero\Apache\Controller\Apache as ApacheController;
use Impero\Apache\Record\Site;
use Impero\Apache\Record\Site\Resolver as SiteResolver;
use Impero\Controller\Impero;
use Pckg\Framework\Provider;

class Apache extends Provider
{

    public function routes()
    {
        return [
            'url' => maestro_urls(ApacheController::class, 'apache', 'site', SiteResolver::class, 'apache/sites'),
        ];
    }

    public function consoles()
    {
        return [
            DumpVirtualhosts::class,
            RestartApache::class,
            ApacheGraceful::class,
            LetsEncryptRenew::class,
        ];
    }

}