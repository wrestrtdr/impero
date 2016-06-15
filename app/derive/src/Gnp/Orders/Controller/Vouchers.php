<?php namespace Gnp\Orders\Controller;

use Gnp\Orders\Entity\Orders;
use Gnp\Orders\Record\Order;
use Pckg\Framework\Controller;
use Gnp\Orders\Entity\Orders as OrdersEntity;
use Gnp\Orders\Form\Attributes;
use Pckg\Collection;
use Pckg\Dynamic\Entity\Tables;
use Pckg\Dynamic\Service\Filter as FilterService;
use Pckg\Dynamic\Service\Sort as OrderService;
use Pckg\Dynamic\Service\Group as GroupService;
use Pckg\Maestro\Helper\Maestro;

class Vouchers extends Controller
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

    public function getIndexAction(OrdersEntity $orders) {
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
                      ->joinActiveOffer()// this needs to be solved by relation filter ...
                      ->forAllocation()// here we need relation orders.orders_users.dt_confirmed
                      ->all();

        $groupedBy = $all->groupBy(
            function(Order $order) {
                return $order->offer->title;
            }
        );

        return $this->tabelize($orders, ['id'], 'Orders')
                    ->setRecords($groupedBy)
                    ->setGroupByLevels(1)
                    ->setEntityActions(
                        [
                            'generateVoucher',
                            'sendVoucher',
                        ]
                    )
                    ->setRecordActions(
                        [
                            'generateVoucher',
                            'sendVoucher',
                            'previewVoucher',
                        ]
                    )
                    ->setFields(
                        [
                            'id',
                            'num',
                            'offer'     => function($order) {
                                return $order->offer ? $order->offer->title : ' -- no offer -- ';
                            },
                            'payee'     => function($order) {
                                $user = $order->user;

                                if (!$user) {
                                    return ' -- no user -- ';
                                }

                                return $user->surname . ' ' . $user->name . "<br />" .
                                       $user->email . '<br />' .
                                       $user->phone;
                            },
                            'packets'   => function(Order $order) {
                                return $order->getPacketsSummary();
                            },
                            'voucherId' => function(Order $order) {
                                return $order->getVoucherId();
                            },
                            'voucher_sent_at',
                        ]
                    ) . view('Gnp\Orders:vouchers');
    }

    public function getHtmlAction(Order $order) {
        return view(
            'voucher/voucher',
            [
                'order' => $order,
            ]
        );
    }

    public function postGenerateAction($orders) {
        $orders = (new Orders())->where('id', explode(',', $orders))->all();
        $orders->each(
            function(Order $order) {
                $order->queueGenerateVoucher();
            }
        );

        return $this->response()->respondWithAjaxSuccessAndRedirectBack();
    }

    public function postSendAction($orders) {
        $orders = (new Orders())->where('id', explode(',', $orders))->all();
        $orders->each(
            function(Order $order) {
                $order->queueSendVoucher();
            }
        );

        return $this->response()->respondWithAjaxSuccessAndRedirectBack();
    }

}