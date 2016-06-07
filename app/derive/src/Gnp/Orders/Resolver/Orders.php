<?php namespace Gnp\Orders\Resolver;

use Gnp\Orders\Entity\Orders as OrdersEntity;
use Pckg\Framework\Provider\RouteResolver;

class Orders implements RouteResolver
{

    public function resolve($value) {
        return (new OrdersEntity())->where('id', $value)
            ->withAppartment()
            ->withCheckin()
            ->withPeople()
            ->oneOrFail();
    }

    public function parametrize($record) {
        return $record->id;
    }

}