<?php namespace Derive\Basket\Provider;

use Derive\Basket\Controller\Basket as BasketController;
use Derive\Basket\Resolver\Offer;
use Pckg\Framework\Provider;
use Pckg\Generic\Middleware\EncapsulateResponse;

class Basket extends Provider
{

    public function routes()
    {
        return [
            'url' => [
                '/order'                 => [
                    'controller' => BasketController::class,
                    'view'       => 'order',
                    'name'       => 'derive.basket.order',
                    'resolvers'  => [
                        Offer::class,
                    ],
                    'afterwares' => [
                        EncapsulateResponse::class,
                    ],
                    'pckg'       => [
                        'generic' => [
                            'template' => 'Pckg\Generic:gonparty',
                        ],
                    ],
                ],
                '/estimate'              => [
                    'controller' => BasketController::class,
                    'view'       => 'estimate',
                    'name'       => 'derive.basket.estimate',
                ],
                '/select-payment-method' => [
                    'controller' => BasketController::class,
                    'view'       => 'paymentMethod',
                    'name'       => 'derive.basket.paymentMethod',
                ],
            ],
        ];
    }

}