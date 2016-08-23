<?php namespace Derive\Basket\Resolver;

use Derive\Orders\Entity\Orders;
use Pckg\Framework\Provider\RouteResolver;

class Order implements RouteResolver
{

    public function resolve($value)
    {
        return (new Orders())
            ->where('dt_confirmed', null)
            ->where('id', $value)
            ->oneOrFail(
                function() {
                    return response()->notFound('Order not found.');
                }
            );
    }

    public function parametrize($record)
    {
        return $record->id;
    }

}