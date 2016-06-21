<?php namespace Gnp\Orders\Controller;

use Gnp\Orders\Entity\Orders as OrdersEntity;
use Gnp\Orders\Entity\OrdersTags;
use Gnp\Orders\Form\Attributes;
use Gnp\Orders\Record\Order;
use Pckg\Collection;
use Pckg\Database\Query\Raw;
use Pckg\Database\Record;
use Pckg\Database\Relation\BelongsTo;
use Pckg\Dynamic\Entity\Tables;
use Pckg\Dynamic\Service\Dynamic;
use Pckg\Framework\Controller;
use Pckg\Maestro\Helper\Maestro;

class Orders extends Controller
{

    use Maestro;

    protected $dynamicService;

    public function __construct(Dynamic $dynamicService) {
        $this->dynamicService = $dynamicService;
    }

    public function getGroupAction(OrdersEntity $orders, Attributes $attributesForm) {
        /**
         * Set table.
         */
        $table = (new Tables())->where('framework_entity', get_class($orders))->oneOrFail();
        $this->dynamicService->setTable($table);

        /**
         * Apply entity extension.
         */
        $this->dynamicService->applyOnEntity($orders);

        $all = $orders->forOrders()
                      ->forAllocation()// here we need relation orders.orders_users.dt_confirmed
                      ->all();
        /**
         * Apply collection extension.
         */
        $groupedBy = $all->groupBy(
            function($order) {
                return $order->checkin && $order->checkin->value ? 'Checkin: ' . $order->checkin->value : 'No checking point';
            }
        )->each(
            function($groupOrders) {
                return (new Collection($groupOrders))->groupBy(
                    function($order) {
                        return $order->appartment && $order->appartment->value ? 'Appartment: ' . $order->appartment->value : 'No appartment';
                    }
                );
            },
            true
        );

        $attributesForm->initFields();

        $tabelize = $this->tabelize($orders, ['id'], 'Orders')
                         ->setRecords($groupedBy)
                         ->setPerPage(50)
                         ->setPage(1)
                         ->setTotal($all->total())
                         ->setGroupByLevels([0, 1])
                         ->setEntityActions(
                             [
                                 'add',
                                 'filter',
                                 'sort',
                                 'group',
                                 'export',
                                 'view',
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
                         );

        if ($this->request()->isAjax()) {
            return [
                'records' => $tabelize->transformRecords(),
            ];
        }

        /**
         * Those 2 views should be loaded in different action.
         */
        return $tabelize . view('allocation', ['attributesForm' => $attributesForm]);
    }

    public function getSummaryAction(OrdersEntity $orders) {
        /**
         * Set table.
         */
        $table = (new Tables())->where('framework_entity', get_class($orders))->oneOrFail();
        $this->dynamicService->setTable($table);

        /**
         * Apply entity extension.
         */
        $this->dynamicService->applyOnEntity($orders);

        $all = $orders->forSummary()
                      ->select(
                          [
                              'orders.offer_id',
                              'offer_title' => 'offers.title',
                              'topay'       => 'ROUND(SUM(orders_bills.price), 2)',
                              'payed'       => 'ROUND(SUM(orders_bills.payed), 2)',
                              'percentage'  => 'IF(SUM(orders_bills.payed) IS NULL, 0, ROUND(SUM(orders_bills.payed)/SUM(orders_bills.price)*100, 2))',
                              'status_type' => 'IF(orders.dt_canceled, \'canceled\', IF(orders.dt_rejected, \'rejected\', IF(orders.dt_payed, \'payed\', IF(orders.dt_confirmed, \'waiting\', \'not-confirmed\'))))',
                          ]
                      )
                      ->groupBy('orders.offer_id, status_type')
                      ->limit(null)
                      ->all();

        $groupedBy = $all->groupBy(
            function($summary) {
                return '#' . $summary->offer_id . ' ' . $summary->offer_title;
            }
        );

        $tabelize = $this->tabelize($orders, ['id'], 'Orders')
                         ->setRecords($groupedBy)
                         ->setGroupByLevels([0])
                         ->setEntityActions([])
                         ->setRecordActions([])
                         ->setFields(
                             [
                                 'status_type',
                                 'payed',
                                 'topay',
                                 'percentage',
                             ]
                         );

        if ($this->request()->isAjax()) {
            return [
                'records' => $tabelize->transformRecords(),
            ];
        }

        /**
         * Those 2 views should be loaded in different action.
         */
        return $tabelize;

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
                ->where(
                    'id',
                    new Raw('SELECT order_id FROM orders_tags WHERE type = \'appartment\' AND `value`'),
                    'NOT IN'
                )
                ->joinActiveOffer()
                ->forAllocation()
                ->all(),
        ];
    }

    public function postAllocationSimilarAction() {
        return [
            'similarOrders' => (new OrdersTags())
                ->where('type', 'appartment')
                ->where('value', $this->post()->get('appartment'))
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