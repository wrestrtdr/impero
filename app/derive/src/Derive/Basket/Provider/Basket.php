<?php namespace Derive\Basket\Provider;

use Pckg\Framework\Provider;

class Basket extends Provider
{

    public function routes()
    {
        return [
            'url' => [
                '/derive/order'                 => [
                    'controller' => \Derive\Basket\Controller\Basket::class,
                    'view'       => 'order',
                    'name'       => 'derive.basket.order',
                ],
                '/derive/estimate'              => [
                    'controller' => \Derive\Basket\Controller\Basket::class,
                    'view'       => 'estimate',
                    'name'       => 'derive.basket.estimate',
                ],
                '/derive/select-payment-method' => [
                    'controller' => \Derive\Basket\Controller\Basket::class,
                    'view'       => 'paymentMethod',
                    'name'       => 'derive.basket.paymentMethod',
                ],
            ],
        ];
    }

}