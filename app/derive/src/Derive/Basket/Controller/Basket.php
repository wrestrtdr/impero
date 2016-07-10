<?php namespace Derive\Basket\Controller;

use Derive\Orders\Entity\Offers;
use Derive\Orders\Record\Order;
use Derive\Orders\Record\OrdersUser;
use Derive\Orders\Record\OrdersUsersAddition;
use Pckg\Auth\Entity\Users;
use Pckg\Auth\Record\User;
use Pckg\Framework\Controller;

class Basket extends Controller
{

    /**
     * Accepts offer id, number of customers and selected packets.
     * Returns all data.
     * We WON'T create order on this point.
     */
    public function getOrderFormAction(Offers $offers)
    {
        /**
         * Expected data:
         * $post = [
         *  'offer_id' => 88,
         *  'packets'  => [
         *      198 => 2,
         *  ],
         * ];
         */

        $offer = $offers->forOrderForm()
                        ->where('id', $this->post('offer_id'))
                        ->oneOrFail();

        return view(
            'Derive\Basket:basket\orderForm',
            [
                'offer'          => $offer,
                'orderedPackets' => $this->post('packets'),
            ]
        );
    }

    /**
     * Accepts customer data and promo code.
     * Returns estimate data.
     */
    public function postCustomersAction(Users $users)
    {
        /**
         * Expected data:
         * $post = [
         *  'offer_id'   => 88,
         *  'order'      => [
         *      [
         *          'packet_id' => 198,
         *          'email'     => 'schtr4jh@schtr4jh.net',
         *          'name'      => 'BOjan',
         *          'surname'   => 'Rajh',
         *          'phone'     => '070553244',
         *          'notes'     => 'test',
         *          'additions' => [
         *              621,
         *          ],
         *      ],
         *      [
         *          'packet_id' => 198,
         *          'email'     => 'schtr4jh@schtr4jh.net',
         *          'name'      => 'BOjan2',
         *          'surname'   => 'Rajh2',
         *          'phone'     => '070553244',
         *          'notes'     => 'test',
         *          'additions' => [
         *              620,
         *          ],
         *      ],
         *  ],
         *  'promo_code' => null,
         * ];
         */

        /**
         * Create new order by offer_id and promo_code_id.
         * We will add user later.
         */
        $order = new Order(
            [
                'offer_id'      => $this->post('offer_id'),
                'promo_code_id' => $this->post('promo_code_id'),
            ]
        );
        $order->save();

        /**
         * Save each orders_user.
         */
        foreach ($this->post('order') as $orderUser) {
            $user = $users->where('email', $orderUser['email'])->one();

            /**
             * Create user if he doesn't exist yet.
             */
            if (!$user) {
                $user = User::getOrCreate(
                    [
                        'email'   => $orderUser['email'],
                        'name'    => $orderUser['name'],
                        'surname' => $orderUser['surname'],
                        'phone'   => $orderUser['phone'],
                        'address' => $orderUser['address'],
                    ]
                );
            }

            /**
             * Set order payee if not set.
             */
            if (!$order->user_id) {
                $order->user_id = $user->id;
                $order->save();
            }

            /**
             * Create new OrdersUser.
             */
            $ordersUser = new OrdersUser(
                [
                    'order_id'  => $order->id,
                    'user_id'   => $user->id,
                    'packet_id' => $orderUser['packet_id'],
                    'notes'     => $orderUser['notes'],
                ]
            );
            $ordersUser->save();

            /**
             * Save additions.
             */
            foreach ($orderUser['additions'] as $addition) {
                $ordersUserAddition = new OrdersUsersAddition(
                    [
                        'order_user_id' => $ordersUser->id,
                    ]
                );
                $ordersUserAddition->save();
            }
        }

        $return = [
            'order'        => $order,
            'summary'      => [
                'bills '    => [
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
                'total'     => 528.00,
                'remaining' => 528.00,
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

    public function postEstimateAction(Order $order)
    {
        /**
         * Expected data:
         * $posted = [
         *  'installments' => 2,
         *  'tos'          => true,
         * ];
         */

        /**
         * Simply set installments
         */
        $order->setInstallments($this->post('installments'));
    }

}