<?php namespace Gnp\Orders\Controller;

use Gnp\Orders\Entity\Orders as OrdersEntity;
use Gnp\Orders\Entity\OrdersTags;
use Gnp\Orders\Form\Attributes;
use Gnp\Orders\Record\Order;
use Pckg\Collection;
use Pckg\Database\Query\Raw;
use Pckg\Database\Relation\BelongsTo;
use Pckg\Dynamic\Entity\Tables;
use Pckg\Dynamic\Service\Filter as FilterService;
use Pckg\Dynamic\Service\Sort as OrderService;
use Pckg\Dynamic\Service\Group as GroupService;
use Pckg\Framework\Controller;
use Pckg\Maestro\Helper\Maestro;

class Orders extends Controller
{

    use Maestro;

    protected $filterService;

    protected $sortService;

    protected $groupService;

    public function __construct(FilterService $filterService, OrderService $sortService, GroupService $groupService) {
        $this->filterService = $filterService;
        $this->sortService = $sortService;
        $this->groupService = $groupService;
    }

    public function getGroupAction(OrdersEntity $orders, Attributes $attributesForm) {
        /**
         * Set table.
         */
        $table = (new Tables())->where('framework_entity', get_class($orders))->oneOrFail();
        $this->filterService->setTable($table);
        $this->sortService->setTable($table);
        $this->groupService->setTable($table);

        /**
         * Apply entity extension.
         */
        $this->filterService->applyOnEntity($orders);
        $this->sortService->applyOnEntity($orders);
        $this->groupService->applyOnEntity($orders);

        $all = $orders->withAppartment()
                      ->withCheckin()
                      ->withPeople()
                      ->withOffer()
                      ->joinActiveOffer() // this needs to be solved by relation filter ...
                      ->forAllocation() // here we need relation orders.orders_users.dt_confirmed
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

        $data = [
            'filter' => $this->filterService->getAppliedFilters(),
            'group'  => $this->groupService->getAppliedGroups(),
            'order'  => $this->sortService->getAppliedSorts(),
        ];

        return $this->tabelize($orders, ['id'], 'Orders')
                    ->setRecords($groupedBy)
                    ->setGroupByLevels([0,1])
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
                ->where('id', new Raw('SELECT order_id FROM orders_tags WHERE type = \'appartment\' AND `value`'), 'NOT IN')
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