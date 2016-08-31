<?php namespace Derive\Basket\Controller;

use Derive\Basket\Entity\PromoCodes;
use Derive\Basket\Record\PromoCode;
use Derive\Basket\Service\Summary;
use Derive\Offers\Entity\Packets;
use Derive\Offers\Record\Offer;
use Derive\Orders\Entity\Offers;
use Derive\Orders\Entity\Orders;
use Derive\Orders\Record\Order;
use Derive\Orders\Record\OrdersUser;
use Derive\Orders\Record\OrdersUsersAddition;
use Pckg\Auth\Entity\Users;
use Pckg\Auth\Record\User;
use Pckg\Collection;
use Pckg\Framework\Controller;
use Pckg\Framework\View\Twig;
use Pckg\Manager\Asset;

class Basket extends Controller
{

    /**
     * Accepts offer id, number of customers and selected packets.
     * Returns all data.
     * We WON'T create order on this point.
     *
     * Expected data:
     * $post = [
     *  'offer_id' => 88,
     *  'packets'  => [
     *      198 => 2,
     *  ],
     * ];
     */
    public function getOrderAction(Offer $offer, Packets $packetsEntity)
    {
        $packets = $this->get('packets', []);

        $customers = new Collection();
        $orderType = null;
        $ticket = null;
        foreach ($packets AS $packetId => $quantity) {
            $packet = $packetsEntity->forSecondStep()
                                    ->where('id', $packetId)
                                    ->oneOrFail();

            /**
             * Set view data.
             */
            for ($i = 0; $i < $quantity; $i++) {
                $hash = sha1(microtime());
                $customers->push(
                    [
                        "hash"        => $hash,
                        "packet"      => $packet,
                        'additions'   => $packet->additions,
                        'departments' => $packet->departments,
                        'includes'    => $packet->includes,
                        'deductions'  => $packet->deductions,
                    ],
                    $hash
                );
            }

            /**
             * Set form type.
             */
            if ($packet->ticket) {
                $orderType = 'simple';
            } else {
                $orderType = 'complex';
            }
        }

        $packets = $packetsEntity->forSecondStep()
                                 ->where('offer_id', $offer->id)
                                 ->where('ticket', $orderType == 'simple' ? 1 : null)
                                 ->allOrFail();

        $this->assetManager()->addAssets(
            [
                'js/narocilnica.js',
                'js/predracun.js',
            ],
            'footer',
            path('www')
        );

        Twig::addStaticData('cssPage', 'order estimateform');

        return view(
            'Derive\Basket:checkout',
            [
                'offer'     => $offer,
                "customers" => $customers,
                'packets'   => $packets,
                'orderType' => $orderType,
                'step'      => 'order',
            ]
        );
    }

    /**
     * @param Order $order
     *
     * Accept order and display order form.
     * Useful for editing and re-buy.
     * Used for re-buy. =)
     */
    public function getOrderFormAction(Order $order)
    {
        Twig::addStaticData('cssPage', 'order estimateform');

        return view(
            'Derive\Basket:checkout',
            [
                'offer'     => $order->offer,
                "customers" => $order->getCustomers(),
                'packets'   => $order->offer->packets,
                'orderType' => $order->getType(),
                'step'      => 'order',
            ]
        );
    }

    /**
     * @param PromoCode $promoCode
     * @param Offer     $offer
     *
     * @return array
     */
    public function postApplyPromoCodeAction(PromoCode $promoCode, Offer $offer)
    {
        $price = $this->post('price');
        $valid = false;

        if (!($error = $promoCode->getError($offer))) {
            $price = $promoCode->applyToPrice($price);
            $notice = __('promo_code_applied');
            $valid = true;
        } else {
            $notice = $error;
        }

        return [
            'success' => true,
            'valid'   => $valid,
            'price'   => $price,
            'notice'  => $notice,
        ];

    }

    /**
     * Accepts customer data and promo code.
     * Returns estimate data.
     *
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
    public function postOrderAction(Offer $offer, Users $users)
    {
        $order = $this->post()->toArray();
        $hash = sha1(microtime());

        if (!$this->post('customers')) {
            return [
                "success" => false,
                "text"    => __("order_data_missing"),
            ];
        }

        $this->session()->set('last_order', $order);

        /**
         * Check for promo code.
         */
        $promoCode = (new PromoCodes())->where('code', $this->post('promocode'))->one();

        /**
         * Create new order by offer_id and promo_code_id.
         * We will add user later.
         */
        $order = Orders::createNew(
            [
                'offer_id'      => $this->post('offer_id'),
                'promo_code_id' => $promoCode && !$promoCode->getError($offer) ? $promoCode->id : null,
                'hash'          => $hash,
                'referer'       => $this->cookie('referer'),
            ]
        );

        /**
         * Save each orders_user.
         */
        $payeeRegistered = false;
        foreach ($this->post('customers') as $tempHash => $customer) {
            $pass = substr(sha1(microtime()), 0, 10);
            $user = $users->where('email', $customer['email'])->one();

            if (!$order->user_id && $user) {
                $payeeRegistered = true;
            }

            /**
             * Create user if he doesn't exist yet.
             */
            if (!$user) {
                $user = User::create(
                    [
                        'email'   => $customer['email'],
                        'name'    => $customer['name'] ?? null,
                        'surname' => $customer['surname'] ?? null,
                        'phone'   => $customer['phone'] ?? null,
                        'address' => $customer['address'] ?? null,
                        'pass'    => $pass,
                    ]
                );
            }

            /**
             * Set order data.
             */
            if (!$order->user_id) {
                $order->user_id = $user->id;
                $order->save();
            }

            /**
             * Send signup email to payee.
             */
            if (!$order->user_id && !$payeeRegistered) {
                email(
                    'signup-payee',
                    $user->email,
                    [
                        'user'     => $user,
                        'offer'    => $offer,
                        'password' => $pass,
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
                    'packet_id' => $customer['packet_id'],
                    'notes'     => $customer['notes'] ?? null,
                    'dt_added'  => date('Y-m-d H:i:s'),
                    'city_id'   => $customer['department_id'] ?? null,
                ]
            );
            $ordersUser->save();

            /**
             * Save additions.
             */
            foreach ($customer['additions'] ?? [] as $addition) {
                $ordersUserAddition = new OrdersUsersAddition(
                    [
                        'order_user_id' => $ordersUser->id,
                        'addition_id'   => $addition, // packet_addition_id
                    ]
                );
                $ordersUserAddition->save();
            }

            /**
             * Save deductions.
             */
            foreach ($customer['deductions'] ?? [] as $deduction) {
                $ordersUserAddition = new OrdersUsersAddition(
                    [
                        'order_user_id' => $ordersUser->id,
                        'deduction_id'  => $deduction, // packet_addition_id
                    ]
                );
                $ordersUserAddition->save();
            }
        }

        /**
         * Summarize data
         */
        $arrSumPackets = [];
        $arrSumAdditions = [];
        $arrSumDeductions = [];
        foreach ($this->post('customers') as $userOrder) {
            /**
             * Summarize packets.
             */
            if (isset($arrSumPackets[$userOrder['packet_id']])) {
                $arrSumPackets[$userOrder['packet_id']]++;
            } else {
                $arrSumPackets[$userOrder['packet_id']] = 1;
            }

            /**
             * Summarize additions.
             */
            foreach ($userOrder['additions'] ?? [] AS $userAddition) {
                if (isset($arrSumAdditions[$userAddition])) {
                    $arrSumAdditions[$userAddition]++;
                } else {
                    $arrSumAdditions[$userAddition] = 1;
                }
            }

            /**
             * Summarize deductions.
             */
            foreach ($userOrder['deductions'] ?? [] AS $userDeduction) {
                if (isset($arrSumDeductions[$userDeduction])) {
                    $arrSumDeductions[$userDeduction]++;
                } else {
                    $arrSumDeductions[$userDeduction] = 1;
                }
            }
        }

        if (!$arrSumPackets) {
            return [
                "success" => false,
                "text"    => __("choose_at_least_one_packet"),
            ];
        }

        /**
         * Create summary.
         */
        $summary = $order->getSummary();

        /**
         * Save price data.
         */
        $order->set(
            [
                'price'    => $summary->getSum(),
                'original' => $summary->getSum(),
            ]
        )->save();

        return [
            'order'        => $order,
            'summary'      => $summary,
            'installments' => $summary->getInstallments($order),
            'portions'     => range(1, 5),
            'replaceUrl'   => url('derive.basket.estimate.order', ['order' => $order]),
        ];
    }

    public function getEstimateAction(Order $order)
    {
        $summary = $order->getSummary();

        Twig::addStaticData('cssPage', 'order estimateform');

        return view(
            'Derive\Basket:checkout',
            [
                'customers'    => $order->getCustomers(),
                'orderType'    => $order->getType(),
                'offer'        => $order->offer,
                'step'         => 'estimate',
                'order'        => $order,
                'summary'      => $summary,
                'installments' => $summary->getInstallments($order),
                'portions'     => range(1, 5),
            ]
        );
    }

    public function postInstallmentsAction(Order $order)
    {
        return [
            'installments' => $order->getSummary()->getInstallments($order, $this->post('installments')),
        ];
    }

    /**
     * Expected data:
     * $posted = [
     *  'installments' => 2,
     *  'tos'          => true,
     * ];
     */
    public function postEstimateAction(Order $order)
    {
        /**
         * Simply set installments
         */
        $order->setInstallments($this->post('installments'));

        return [
            'redirect' => '/select-payment-method/' . $order->hash, // $order->getPaymentUrlAttribute(),
        ];
    }

    public function getPaymentMethodAction()
    {
        return ' .. in work .. ';
    }

}