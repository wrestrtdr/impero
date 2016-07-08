<?php namespace Derive\Orders\Resolver;

use Derive\Orders\Entity\Orders as OrdersEntity;
use Pckg\Database\Relation\HasMany;
use Pckg\Framework\Provider\RouteResolver;

class Orders implements RouteResolver
{

    public function resolve($value)
    {
        return (new OrdersEntity())->where('id', $value)
                                   ->joinAppartment()
                                   ->joinCheckin()
                                   ->joinPeople()
                                   ->withOffer()
                                   ->withOrdersUsers(
                                       function(HasMany $hasMany) {
                                           $hasMany->where('dt_confirmed');
                                           $hasMany->withPacket();
                                       }
                                   )
                                   ->oneOrFail(
                                       function() {
                                           return response()->notFound('Order not found');
                                       }
                                   );
    }

    public function parametrize($record)
    {
        return $record->id;
    }

}