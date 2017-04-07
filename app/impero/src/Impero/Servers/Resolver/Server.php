<?php namespace Impero\Servers\Resolver;

use Impero\Servers\Dataset\Servers;
use Pckg\Framework\Provider\RouteResolver;

class Server implements RouteResolver
{

    public function resolve($value)
    {
        return (new Servers())->getServerForUser($value);
    }

    public function parametrize($record)
    {
        return $record->id;
    }

}