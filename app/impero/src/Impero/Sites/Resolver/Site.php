<?php namespace Impero\Sites\Resolver;

use Impero\Apache\Entity\Sites;
use Pckg\Framework\Provider\RouteResolver;

class Site implements RouteResolver
{

    public function resolve($value)
    {
        return (new Sites())->where('id', $value)->oneOrFail();
    }

    public function parametrize($record)
    {
        return $record->id;
    }

}