<?php namespace Impero\Mysql\Provider;

use Impero\Mysql\Controller\Database;
use Impero\Mysql\Controller\User;
use Impero\Mysql\Record\Database\Resolver as DatabaseResolver;
use Impero\Mysql\Record\User\Resolver as UserResolver;
use Pckg\Framework\Provider;

class Config extends Provider
{

    public function routes()
    {
        return [
            'url' => maestro_urls(Database::class, 'database', 'database', DatabaseResolver::class, 'mysql/database')
                + maestro_urls(User::class, 'user', 'user', UserResolver::class, 'mysql/user'),
        ];
    }

}