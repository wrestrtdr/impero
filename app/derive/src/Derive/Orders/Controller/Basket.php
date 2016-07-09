<?php namespace Derive\Orders\Controller;

use Pckg\Framework\Controller;

class Basket extends Controller
{

    /**
     * Accepts offer id, number of customers and selected packets.
     * Returns all data.
     */
    public function getCustomersAction()
    {
        $post = [
            'offer_id' => 88,
            'packets'  => [
                198 => 2,
            ],
        ];
    }

    /**
     * Accepts customer data and promo code.
     * Returns estimate data.
     */
    public function postCustomersAction()
    {
        $posted = [
            'offer_id'   => 88,
            'order'      => [
                [
                    'packet_id' => 198,
                    'email'     => 'schtr4jh@schtr4jh.net',
                    'name'      => 'BOjan',
                    'surname'   => 'Rajh',
                    'phone'     => '070553244',
                    'notes'     => 'test',
                    'additions' => [
                        621,
                    ],
                ],
                [
                    'packet_id' => 198,
                    'email'     => 'schtr4jh@schtr4jh.net',
                    'name'      => 'BOjan2',
                    'surname'   => 'Rajh2',
                    'phone'     => '070553244',
                    'notes'     => 'test',
                    'additions' => [
                        620,
                    ],
                ],
            ],
            'promo_code' => null,
        ];

        $return = [
            'payee'        => [
                'email'   => 'schtr4jh@schtr4jh.net',
                'name'    => 'Bojan',
                'surname' => 'Rajh',
                'phone'   => '070553244',
            ],
            'order'        => [
                'num' => '2016-00329',
            ],
            'summary'      => [
                'bills '      => [
                    [
                        'title'    => 'IBZ CLOSING FIESTA -5x NOČITEV',
                        'quantity' => 2,
                        'price'    => 249.00,
                        'total'    => 498.00,
                    ],
                    [
                        'title'    => 'DODATEK #1',
                        'quantity' => 1,
                        'price'    => 10.00,
                        'total'    => 10.00,
                    ],
                    [
                        'title'    => 'DODATEK #2',
                        'quantity' => 2,
                        'price'    => 10.00,
                        'total'    => 20.00,
                    ],
                    [
                        'title'    => 'Promo koda',
                        'quantity' => 1,
                        'price'    => 12.12,
                        'total'    => 12.12,
                    ],
                ],
                'total'       => 528.00,
                'remaining'   => 528.00,
            ],
            'installments' => [
                [
                    'title'    => '1. obrok',
                    'due_date' => '2016-07-05',
                    'price'    => 339.5,
                ],
                [
                    'title'    => '2. obrok',
                    'due_date' => '2016-08-05',
                    'price'    => 339.5,
                ],
            ],
        ];
    }

    public function postEstimateAction()
    {
        $posted = [
            'installments' => 2,
            'tos'          => true,
        ];
    }

}