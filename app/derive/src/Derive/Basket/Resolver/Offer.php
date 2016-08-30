<?php namespace Derive\Basket\Resolver;

use Derive\Offers\Entity\Offers;
use Pckg\Framework\Provider\RouteResolver;
use Pckg\Framework\Request\Data\Get;
use Pckg\Framework\Request\Data\Post;

class Offer implements RouteResolver
{

    protected $get;

    protected $post;

    public function __construct(Get $get, Post $post)
    {
        $this->get = $get;
        $this->post = $post;
    }

    public function resolve($value)
    {
        return (new Offers())
            //->forSecondStep()
            ->where('id', $this->get->get('offer_id') ?? $this->post->get('offer_id'))
            ->oneOrFail(
                function() {
                    return response()->notFound('Offer not found.');
                }
            );
    }

    public function parametrize($record)
    {
        return $record->id;
    }

}