<?php namespace Derive\Offers\Provider;

use Derive\Offers\Controller\Offers as OffersController;
use Pckg\Framework\Provider;

class Offers extends Provider
{

    public function routes()
    {
        return [
            'url' => [
                '/derive-offers' => [
                    'controller' => OffersController::class,
                    'view'       => 'list',
                    'name'       => 'derive.offers.list',
                    'pckg'       => [
                        'generic' => [
                            'template' => 'Pckg\Generic:genericEmbed',
                        ],
                    ],
                ],
            ],
        ];
    }

}