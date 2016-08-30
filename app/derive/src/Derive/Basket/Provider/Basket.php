<?php namespace Derive\Basket\Provider;

use Derive\Basket\Controller\Basket as BasketController;
use Derive\Basket\Resolver\Offer;
use Derive\Basket\Resolver\Order;
use Derive\Basket\Resolver\PromoCode;
use Pckg\Framework\Provider;
use Pckg\Generic\Middleware\EncapsulateResponse;

class Basket extends Provider
{

    public function routes()
    {
        return [
            'url' => [
                '/order'                              => [
                    'controller' => BasketController::class,
                    'view'       => 'order',
                    'name'       => 'derive.basket.order',
                    'resolvers'  => [
                        'offer' => Offer::class,
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
                '/order/[order]'                      => [
                    'controller' => BasketController::class,
                    'view'       => 'order',
                    'name'       => 'derive.basket.order.rebuy',
                    'resolvers'  => [
                        'order' => Order::class,
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
                '/order/json/applypromocode'          => [
                    'controller' => BasketController::class,
                    'view'       => 'applyPromoCode',
                    'name'       => 'derive.basket.applyPromoCode',
                    'resolvers'  => [
                        'promocode' => PromoCode::class,
                        'offer'     => Offer::class,
                    ],
                ],
                '/order/json/getinstallments/[order]' => [
                    'controller' => BasketController::class,
                    'view'       => 'installments',
                    'name'       => 'derive.basket.installments',
                    'resolvers'  => [
                        'order' => Order::class,
                    ],
                ],
                '/estimate'                           => [
                    'controller' => BasketController::class,
                    'view'       => 'estimate',
                    'name'       => 'derive.basket.estimate',
                ],
                '/estimate/[order]'                   => [
                    'controller' => BasketController::class,
                    'view'       => 'estimate',
                    'name'       => 'derive.basket.estimate.order',
                    'resolvers'  => [
                        'order' => Order::class,
                    ],
                    'pckg'       => [
                        'generic' => [
                            'template' => 'Pckg\Generic:gonparty',
                        ],
                    ],
                ],
                '/pay/[order]'                        => [
                    'controller' => BasketController::class,
                    'view'       => 'paymentMethod',
                    'name'       => 'derive.basket.payment',
                    'resolvers'  => [
                        'order' => Order::class,
                    ],
                    'pckg'       => [
                        'generic' => [
                            'template' => 'Pckg\Generic:gonparty',
                        ],
                    ],
                ],
            ],
        ];
    }

}