<?php namespace Gnp\Orders\Controller;

use Gnp\Orders\Entity\Orders as OrdersEntity;
use Gnp\Orders\Entity\OrdersTags;
use Gnp\Orders\Form\Attributes;
use Gnp\Orders\Record\Order;
use Pckg\Collection;
use Pckg\Database\Query\Raw;
use Pckg\Database\Relation\BelongsTo;
use Pckg\Framework\Controller;
use Pckg\Maestro\Helper\Maestro;

class Orders extends Controller
{

    use Maestro;

    public function getGroupAction(OrdersEntity $orders, Attributes $attributesForm) {
        $all = measure(
            'Getting orders',
            function() use ($orders) {
                return $orders->withAppartment()
                              ->withCheckin()
                              ->withPeople()
                              ->withOffer()
                              ->joinActiveOffer()
                              ->forAllocation()
                              ->all();
            }
        );

        $groupedBy = $all->groupBy(
            function($order) {
                return $order->checkin ? 'Checkin: ' . $order->checkin->value : null;
            }
        )->each(
            function($groupOrdes) {
                $newGroup = (new Collection($groupOrdes))->groupBy(
                    function($order) {
                        return $order->appartment ? 'Appartment: ' . $order->appartment->value : null;
                    }
                );

                return $newGroup;
            },
            true
        );

        $attributesForm->initFields();

        return $this->tabelize($orders, ['id'], 'Orders')
                    ->setRecords($groupedBy)
                    ->setGroupByLevels(2)
                    ->setEntityActions(
                        [
                            /*'add',
                            'filter',
                            'sort',
                            'group',
                            'export',*/
                        ]
                    )
                    ->setRecordActions(
                        [
                            'attributes',
                        ]
                    )
                    ->setFields(
                        [
                            'id',
                            'num',
                            'offer'   => function($order) {
                                return $order->offer ? $order->offer->title : ' -- no offer -- ';
                            },
                            'payee'   => function($order) {
                                $user = $order->user;

                                if (!$user) {
                                    return ' -- no user -- ';
                                }

                                return $user->surname . ' ' . $user->name . "<br />" .
                                       $user->email . '<br />' .
                                       $user->phone;
                            },
                            'packets' => function(Order $order) {
                                return $order->getPacketsSummary();
                            },
                        ]
                    ) . view('allocation', ['attributesForm' => $attributesForm]);
    }

    public function getAllocationAction(Order $order) {
        return $this->getAllocationAttributesAction($order);
    }

    public function getAllocationAttributesAction(Order $order) {
        return [
            'appartment' => $order->appartment ? $order->appartment->value : null,
            'checkin'    => $order->checkin ? $order->checkin->value : null,
            'people'     => $order->people ? $order->people->value : null,
        ];
    }

    public function getAllocationNonAllocatedAction() {
        return [
            'nonAllocatedOrders' => (new OrdersEntity())
                ->where('id', new Raw('SELECT order_id FROM orders_tags'), 'NOT IN')
                ->joinActiveOffer()
                ->forAllocation()
                ->all(),
        ];
    }

    public function getAllocationSimilarAction($appartment) {
        return [
            'similarOrders' => (new OrdersTags())
                ->where('type', 'appartment')
                ->where('value', $appartment)
                ->withOrder(
                    function(BelongsTo $relation) {
                        $relation->withUser();
                        $relation->withConfirmedPackets();
                        $relation->joinActiveOffer();
                    }
                )
                ->all()
                ->each(
                    function($ordersTag) {
                        return $ordersTag->order;
                    },
                    true
                ),
        ];
    }

    public function postSaveAction() {
        $post = $this->post();

        $orderIds = $post->get('orders');
        $appartment = $post->get('appartment');
        $people = $post->get('people');
        $checkin = $post->get('checkin');

        $orders = (new OrdersEntity())->where('id', $orderIds)->all();
        $orders->each(
            function(Order $order) use ($appartment, $checkin, $people) {
                $order->setAppartment($appartment);
                $order->setCheckin($checkin);
                $order->setPeople($people);
            }
        );

        $orders = (new OrdersTags())->where('value', $appartment)
                                    ->where('type', 'appartment')
                                    ->where('order_id', $orderIds, 'NOT IN')
                                    ->delete();

        return $this->response()->respondWithAjaxSuccess();
    }

}