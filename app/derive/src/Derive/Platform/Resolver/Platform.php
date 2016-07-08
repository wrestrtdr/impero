<?php namespace Derive\Platform\Resolver;

use Derive\Platform\Entity\Platforms;
use Pckg\Framework\Provider\RouteResolver;

class Platform implements RouteResolver
{

    public function resolve($value) {
        return (new Platforms())->forUser(auth()->getUser())->where('id', $value)->oneOrFail();
    }

    public function parametrize($record) {
        return $record->id;
    }

}