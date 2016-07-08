<?php namespace Impero\Mysql\Provider;

use Impero\Mysql\Controller\Database;
use Impero\Mysql\Controller\User;
use Impero\Mysql\Record\Database\Resolver as DatabaseResolver;
use Impero\Mysql\Record\User\Resolver as UserResolver;
use Pckg\Framework\Provider;

class Mysql extends Provider
{

    public function routes()
    {
        return [
            'url' => maestro_urls(Database::class, 'database', 'database', DatabaseResolver::class, 'mysql/databases')
                + maestro_urls(User::class, 'user', 'user', UserResolver::class, 'mysql/users'),
        ];
    }

}