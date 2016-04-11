<?php

use Impero\Apache\Provider\Config as ApacheProvider;
use Impero\Dns\Provider\Config as DnsProvider;
use Impero\Ftp\Provider\Config as FtpProvider;
use Impero\Mysql\Provider\Config as MysqlProvider;
use Pckg\Framework\Application\Website;

class Impero extends Website
{

    public function run()
    {
        parent::run();
        
        $migration = new \Impero\Apache\Migration\CreateSiteTable();
    }

    public function providers()
    {
        return [
            ApacheProvider::class,
            FtpProvider::class,
            MysqlProvider::class,
            DnsProvider::class,
        ];
    }

}