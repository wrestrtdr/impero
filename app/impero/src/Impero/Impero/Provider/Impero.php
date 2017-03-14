<?php namespace Impero\Impero\Provider;

use Impero\Apache\Provider\Apache as ApacheProvider;
use Impero\Ftp\Provider\Ftp as FtpProvider;
use Impero\Git\Provider\Git as GitProvider;
use Impero\Impero\Controller\Impero as ImperoController;
use Impero\Mysql\Provider\Mysql as MysqlProvider;
use Impero\Servers\Provider\Servers;
use Pckg\Auth\Provider\Auth as AuthProvider;
use Pckg\Framework\Provider;
use Pckg\Framework\Provider\Frontend;
use Pckg\Generic\Provider\Generic as GenericProvider;
use Pckg\Manager\Provider\Manager as ManagerProvider;

class Impero extends Provider
{

    public function registered(\Pckg\Manager\Asset $assetManager)
    {
        path('src_derive', path('apps') . 'derive' . path('ds') . 'src' . path('ds'));
        $assetManager->executeCore();
    }

    public function providers()
    {
        return [
            ManagerProvider::class,
            ApacheProvider::class,
            FtpProvider::class,
            MysqlProvider::class,
            GitProvider::class,
            //DynamicProvider::class,
            AuthProvider::class,
            GenericProvider::class,
            Provider\Framework::class,
            // new generation
            Servers::class,
            Frontend::class,
        ];
    }

    public function routes()
    {
        return [
            'url' => [
                '/'      => [
                    'controller' => ImperoController::class,
                    'view'       => 'index',
                ],
                '/intro' => [
                    'controller' => ImperoController::class,
                    'view'       => 'intro',
                ],
            ],
        ];
    }

}