<?php namespace Derive\Basket\Controller;

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

    }

    /**
     * @param PromoCode $promoCode
     * @param Offer     $offer
     *
     * @return array
     */
    public function postApplyPromoCode(PromoCode $promoCode, Offer $offer)
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
            'price'   => cutAndMakePrice($price),
            'notice'  => $notice,
        ];

    }

    /**
     * Accepts customer data and promo code.
     * Returns estimate data.
     */
    public function postOrderAction(Offer $offer, Users $users)
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
        $order = $this->post()->toArray();
        $hash = sha1(microtime());

        if (!$this->post('customers')) {
            return [
                "success" => false,
                "text"    => __("order_data_missing"),
            ];
        }

        $this->session()->set('last_order', $order);

        $showPortions = $offer->getMaxPortions();

        /**
         * Create new order by offer_id and promo_code_id.
         * We will add user later.
         */
        $order = Orders::createNew(
            [
                'offer_id'      => $this->post('offer_id'),
                'promo_code_id' => $this->post('promo_code_id'),
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

        /**
         * Get max portions.
         */
        $maxPortions = $offer->getMaxPortions();

        return [
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
            'summary'      => $summary,
        ];
    }

    public function oldPostOrderFormAction()
    {
        $this->log();
        $order = $_POST;
        $hash = sha1(microtime());
        $refresh = false;

        // catch refresh
        if (!isset($order['order']) || empty($order['order'])) {
            return JSON::to(
                [
                    "success" => false,
                    "text"    => __("refresh_triggered"),
                ]
            );
        } else {
            $_SESSION['last order'] = $order;
            $_SESSION['last order']['hash'] = $hash;
        }

        $rOffer = $this->Offers->get("first", null, ["id" => $order['offer_id']]);

        $pt = $this->OffersPaymentMethods->getPaymentTable($rOffer['id']);

        // hide reservations in HTML template if all methods have disabled reservations
        $showReservations = RESERVATION > 0;

        // hide portions in HTML template if all methods have disabled portions
        $showPortions = ($rOffer['max_portions'] ?? MAX_PORTIONS) > 1;

        if (!isset($order['offer_id']) || !Validate::isInt($order['offer_id'])) {
            return JSON::to(
                [
                    "success" => false,
                    "text"    => __("wrong_offer"),
                ]
            );
        }

        $rPayee = [];
        $arrSentEmails = [];
        $i = 0;
        foreach ($order['order'] AS $tempHash => $orderUser) {
            $rUser = $this->Users->get("first", null, ["email" => $orderUser['email']]);

            // register user
            if (empty($rUser)) {
                $pass = substr(sha1(microtime()), 0, 10);
                $userId = -1;

                if (!empty($orderUser['email'])) {
                    $userId = $this->Users->insert(
                        [
                            "email"    => $orderUser['email'],
                            "name"     => $orderUser['name'],
                            "surname"  => $orderUser['surname'],
                            "phone"    => isset($orderUser['phone']) ? $orderUser['phone'] : null,
                            "password" => Auth::hashPassword($pass),
                        ]
                    );

                    // send mail only if it is not yet sent AND user is a payee. Friends are not mailed anymore
                    if (!in_array($orderUser['email'], $arrSentEmails) && $i == 0) {
                        $this->Mails->automatic(
                            'signup-payee',
                            [
                                "user"     => $userId,
                                "offer"    => $order['offer_id'],
                                "password" => $pass,
                                "to"       => $orderUser['email'],
                            ]
                        );
                        $arrSentEmails[] = $orderUser['email'];
                    }
                } else {
                    $userId = $this->Users->insert(
                        [
                            "email"    => substr(sha1(microtime()), 0, 10) . "@gnp.si",
                            "name"     => $orderUser['name'],
                            "surname"  => $orderUser['surname'],
                            "phone"    => isset($orderUser['phone']) ? $orderUser['phone'] : null,
                            "enabled"  => -1,
                            "password" => Auth::hashPassword($pass),
                        ]
                    );
                }

                $rUser = $this->Users->get("first", null, ["id" => $userId]);
            } else if (isset($orderUser['phone']) && is_numeric($orderUser['phone'])) {

                // take new data only if they are valid
                $this->Users->update(
                    [
                        "name"    => ($orderUser['name'] != '' && !is_null(
                                $orderUser['name']
                            )) ? $orderUser['name'] : $rUser['name'],
                        "surname" => ($orderUser['surname'] != '' && !is_null(
                                $orderUser['surname']
                            )) ? $orderUser['surname'] : $rUser['surname'],
                        "phone"   => ($orderUser['phone'] != '' && is_numeric(
                                $orderUser['phone']
                            )) ? $orderUser['phone'] : $rUser['phone'],
                    ],
                    $rUser['id']
                );

                // get new, updated data
                $rUser = $this->Users->get("first", null, ["id" => $rUser['id']]);
            }

            $order['order'][$tempHash]['user_id'] = $rUser['id'];

            // set current user as payee
            if ($i == 0) {
                $rPayee = $rUser;
            }
            $i++;
        }

        if (empty($rPayee)) {
            return JSON::to(
                [
                    "success" => false,
                    "text"    => __("cannot_find_payee"),
                ]
            );
        }

        $sqlNumOrders = "SELECT MAX(num) AS num FROM orders o WHERE o.dt_added >= '" . date("Y") . "-01-01 00:00:00'";
        $qNumOrders = $this->db->q($sqlNumOrders);
        $rNumOrders = $this->db->f($qNumOrders);

        if (!empty($rNumOrders['num'])) {
            $tempArr = explode("-", $rNumOrders['num']);
            $realMaxNum = (int)ltrim(end($tempArr), "0");
        } else {
            $realMaxNum = 0;
        }

        if ($refresh == false) {
            $orderID = $this->Orders->insert(
                [
                    "user_id"       => $rPayee['id'],
                    "hash"          => $hash,
                    "offer_id"      => $order['offer_id'],
                    "referer"       => isset($_COOKIE['referer']) && $_COOKIE['referer'] ? $_COOKIE['referer'] : '',
                    "num"           => date("Y") . "-" . numToString($realMaxNum + 1),
                    "promo_code_id" => $this->PromoCodes->getIdByCode($_POST['promocode'], true, $order['offer_id']),
                ]
            );
        } else {
            $rTempOrder = $this->Orders->get("first", null, ["hash" => $_SESSION['last order']['hash']]);

            if (empty($rTempOrder)) {
                return JSON::to(
                    [
                        "success" => false,
                        "text"    => __("cannot_find_order"),
                    ]
                );
            }

            $orderID = $rTempOrder['id'];
        }

        $tpl = new TwigTpl('orders/templates/estimateform.twig');

        // sumarize data
        $arrSumPackets = [];
        $arrSumAdditions = [];
        foreach ($order['order'] AS $userOrder) {
            if (!isset($userOrder['packet_id'])) {
                return JSON::to(
                    [
                        "success" => false,
                        "text"    => __("cannot_find_packet"),
                    ]
                );
            }
            if (isset($arrSumPackets[$userOrder['packet_id']])) {
                $arrSumPackets[$userOrder['packet_id']]++;
            } else {
                $arrSumPackets[$userOrder['packet_id']] = 1;
            }

            if (isset($userOrder['additions'])) {
                foreach ($userOrder['additions'] AS $userAddition) {
                    if (isset($arrSumAdditions[$userAddition])) {
                        $arrSumAdditions[$userAddition]++;
                    } else {
                        $arrSumAdditions[$userAddition] = 1;
                    }
                }
            }
        }

        if (empty($arrSumPackets)) {
            return JSON::to(
                [
                    "success" => false,
                    "text"    => __("choose_at_least_one_packet"),
                ]
            );
        }

        // sumarize data
        $sum = 0.0;

        // get packets from database
        $sqlPackets = "SELECT p.id, p.title, p.price " .
                      "FROM packets p " .
                      "INNER JOIN offers o ON o.id = p.offer_id " .
                      "WHERE p.offer_id = " . $order['offer_id'] . " " . // only selected offer
                      "AND p.id IN (" . implode(",", array_keys($arrSumPackets)) . ")" . // only selected packets
                      "AND o.dt_published > '" . DT_NULL . "' " . // is published offer
                      "AND o.dt_published < '" . DT_NOW . "'" .
                      "AND p.dt_published > '" . DT_NULL . "' " . // is published packet
                      "AND p.dt_published < '" . DT_NOW . "'" .
                      "AND o.dt_opened > '" . DT_NULL . "' " . // is opened
                      "AND o.dt_opened < '" . DT_NOW . "'";
        $qPackets = $this->db->q($sqlPackets);
        $arrBills = [];
        while ($rPacket = $this->db->f($qPackets)) {
            $arrBills[] = [
                "title"    => $rPacket['title'],
                "price"    => makePrice($rPacket['price']),
                "sum"      => makePrice($rPacket['price'] * $arrSumPackets[$rPacket['id']]),
                "quantity" => $arrSumPackets[$rPacket['id']],
            ];
            $sum += (float)($rPacket['price'] * $arrSumPackets[$rPacket['id']]);
        }

        if (empty($arrSumPackets)) {
            $arrSumPackets[-1] = 0;
        }

        if (empty($arrSumAdditions)) {
            $arrSumAdditions[-1] = 0;
        }

        // stroški prijavnine
        $sum += PROCESSING_COST;

        $arrBills[] = [
            "title"    => "Booking fee",
            "price"    => makePrice(PROCESSING_COST),
            "sum"      => makePrice(PROCESSING_COST),
            "quantity" => 1,
        ];

        // get additions from database
        $sqlAdditions = "SELECT pa.id, a.title, pa.value " .
                        "FROM additions a " .
                        "INNER JOIN packets_additions pa ON pa.addition_id = a.id " .
                        "INNER JOIN packets p ON p.id = pa.packet_id " .
                        "INNER JOIN offers o ON o.id = p.offer_id " .
                        "WHERE p.offer_id = " . $order['offer_id'] . " " . // only selected offer
                        "AND p.id IN (" . implode(",", array_keys($arrSumPackets)) . ") " . // only selected packets
                        "AND pa.id IN (" . implode(
                            ",",
                            array_keys($arrSumAdditions)
                        ) . ") " . // only selected additions
                        "AND o.dt_published > '" . DT_NULL . "' " . // is published offer
                        "AND o.dt_published < '" . DT_NOW . "' " .
                        "AND p.dt_published > '" . DT_NULL . "' " . // is published packet
                        "AND p.dt_published < '" . DT_NOW . "' " .
                        "AND o.dt_opened > '" . DT_NULL . "' " . // is opened
                        "AND o.dt_opened < '" . DT_NOW . "' " .
                        "AND pa.visible = 1";
        $qAdditions = $this->db->q($sqlAdditions);

        while ($rAddition = $this->db->f($qAdditions)) {
            $tpl->parse(
                [
                    "title"    => $rAddition['title'],
                    "price"    => makePrice($rAddition['value']),
                    "sum"      => makePrice($rAddition['value'] * $arrSumAdditions[$rAddition['id']]),
                    "quantity" => $arrSumAdditions[$rAddition['id']],
                ],
                "row"
            );
            $sum += (float)($rAddition['value'] * $arrSumAdditions[$rAddition['id']]);
        }

        $rOrder = $this->Orders->get("first", null, ["id" => $orderID]);

        if ($rOrder['promo_code_id']) {
            $discount = $this->PromoCodes->getMinusByIdAndPrice($rOrder['promo_code_id'], $sum);
            $promoDiscount = '-' . makePrice($discount);
            $_SESSION['promo_discount'] = $discount;

            $arrBills[] = [
                'title'    => __('promo_code_title'),
                'price'    => $promoDiscount,
                'sum'      => $promoDiscount,
                'quantity' => 1,
            ];

            $sum -= $discount;
        } else {
            $_SESSION['promo_discount'] = null;
        }

        // update sum
        $this->Orders->update(
            [
                "price"    => $sum,
                "original" => $sum,
            ],
            $rOrder['id']
        );

        $rOrder = $this->Orders->get("first", null, ["id" => $orderID]);

        $tpl->commit("row");

        // calculate maximum number of portions only if we have them enabled in Maestro
        $maxPortions = ($showPortions) ? $this->getMaxPortions(
            $rOffer['max_portions'] ?? MAX_PORTIONS,
            $rOffer['dt_start'],
            DT_NOW,
            $rOffer['max_portions'] ?? MAX_PORTIONS
        ) : 1;

        $arrPortions = [];
        for ($i = 1; $i <= $maxPortions; $i++) {
            $arrPortions[$i] = $i;
        }

        // delete orders users
        $qDeleteOrdersUsers = $this->OrdersUsers->get("all", null, ["order_id" => $orderID]);
        while ($rDeleteOrdersUser = $this->db->f($qDeleteOrdersUsers)) {
            // delete orders users additions
            $qDeleteOrdersUsersAdditions = $this->OrdersUsersAdditions->get(
                "all",
                null,
                ["orders_user_id" => $rDeleteOrdersUser['id']]
            );
            while ($rDeleteOrdersUserAddition = $this->db->f($qDeleteOrdersUsersAdditions)) {
                $this->OrdersUsersAdditions->delete($rDeleteOrdersUserAddition['id']);
            }

            $this->OrdersUsers->delete($rDeleteOrdersUser['id']);
        }

        // insert new data
        foreach ($order['order'] AS $tempHash => $orderUser) {
            // insert orders user
            $ordersUserID = $this->OrdersUsers->insert(
                [
                    "order_id"  => $orderID,
                    "packet_id" => $orderUser['packet_id'],
                    "notes"     => $orderUser['notes'] ?? null,
                    "user_id"   => $orderUser['user_id'], // updated in first while loop
                    "city_id"   => isset($orderUser['department_id']) ? $orderUser['department_id'] : -1,
                ]
            );

            // insert orders users addition
            if (isset($orderUser['additions'])) {
                foreach ($orderUser['additions'] AS $ordersUsersAddition) {
                    $this->OrdersUsersAdditions->insert(
                        [
                            "orders_user_id" => $ordersUserID,
                            "addition_id"    => $ordersUsersAddition,
                        ]
                    );
                }
            }
        }

        $tplData = [
            "payee"            => [
                "name"    => $rPayee['name'],
                "surname" => $rPayee['surname'],
                "address" => ($rPayee['address']) ? $rPayee['address'] : null,
                "post"    => $rPayee['post'] ? $rPayee['address'] : null,
            ],
            "order"            => $rOrder,
            "paymentDate"      => date("d.m.Y", strtotime($rOffer['dt_closed'])),
            "offer"            => $rOffer,
            "sumTotal"         => $sum,
            "sumRemaining"     => $sum - RESERVATION,
            "paymentsList"     => HTML::select(
                [
                    "id"      => "bills",
                    "name"    => "bills",
                    "options" => $arrPortions,
                ]
            ),
            "portion"          => $this->portions(
                [
                    "portions"     => 1,
                    "max"          => $rOffer['dt_closed'],
                    "price"        => $sum - RESERVATION,
                    'max_portions' => $rOffer['max_portions'] ?? MAX_PORTIONS,
                ]
            ),
            "action"           => Router::make("placilna sredstva"),
            "showReservations" => $showReservations,
            "showPortions"     => $showPortions,
            "reservationPrice" => makePrice(RESERVATION),
            "mode"             => MODE,
            'bills'            => $arrBills,
        ];

        $tpl->addData($tplData);

        if (!$refresh) {
            return JSON::to(
                [
                    "success" => true,
                    "html"    => $tpl->display(),
                    "css"     => [
                        "n" => "estimateform" . (SKIP_PAYMENT_METHOD_SELECTION ? ' skip-payment-step' : ''),
                        "o" => "order",
                    ],
                    "newurl"  => Router::make("predracun"),
                ]
            );
        }

        return $tpl->display();
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
        dd($order);
        $order->setInstallments($this->post('installments'));
    }

}