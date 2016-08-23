<?php namespace Derive\Basket\Resolver;

use Derive\Basket\Entity\PromoCodes;
use Pckg\Framework\Helper\Traits;
use Pckg\Framework\Provider\RouteResolver;

class PromoCode implements RouteResolver
{

    use Traits;

    public function resolve($value)
    {
        return (new PromoCodes())
            ->where('id', $this->get('promocode') ?? $this->post('promocode'))
            ->oneOrFail(
                function() {
                    return response()->notFound('Promo code not found.');
                }
            );
    }

    public function parametrize($record)
    {
        return $record->id;
    }

}