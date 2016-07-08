<?php namespace Impero\Git\Provider;

use Impero\Git\Console\GitPull;
use Impero\Git\Controller\Git as GitController;
use Impero\Git\Resolver\Site;
use Pckg\Framework\Provider;

class Git extends Provider
{

    public function routes() {
        return [
            'url' => [
                '/git/webhook/[site]' => [
                    'controller' => GitController::class,
                    'view'       => 'webhook',
                    'name'       => 'impero.git.webhook',
                    'resolvers'  => [
                        'site' => Site::class,
                    ],
                ],
            ],
        ];
    }

    public function consoles() {
        return [
            GitPull::class,
        ];
    }

}