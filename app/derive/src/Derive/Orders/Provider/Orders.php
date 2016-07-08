<?php namespace Derive\Orders\Provider;

use Derive\Orders\Console\GenerateVoucher;
use Derive\Orders\Console\SendVoucher;
use Derive\Orders\Controller\Orders as OrdersController;
use Derive\Orders\Controller\Vouchers;
use Derive\Orders\Resolver\Orders as OrdersResolver;
use Pckg\Framework\Provider;

class Orders extends Provider
{

    public function routes() {
        return [
            'url' => [
                '/orders'                                    => [
                    'controller' => OrdersController::class,
                    'view'       => 'group',
                    'name'       => 'derive.orders.group',
                ],
                '/orders/allocation/[order]'                 => [
                    'controller' => OrdersController::class,
                    'view'       => 'allocation',
                    'name'       => 'derive.orders.allocation.get',
                    'resolvers'  => [
                        'order' => OrdersResolver::class,
                    ],
                ],
                '/orders/allocation/attributes/[appartment]' => [
                    'controller' => OrdersController::class,
                    'view'       => 'allocationAttributes',
                    'name'       => 'derive.orders.allocation.attributes',
                ],
                '/orders/allocation/non-allocated'           => [
                    'controller' => OrdersController::class,
                    'view'       => 'allocationNonAllocated',
                    'name'       => 'derive.orders.allocation.nonallocated',
                ],
                '/orders/allocation/similar'                 => [
                    'controller' => OrdersController::class,
                    'view'       => 'allocationSimilar',
                    'name'       => 'derive.orders.allocation.similar',
                ],
                '/orders/allocation/save'                    => [
                    'controller' => OrdersController::class,
                    'view'       => 'save',
                    'name'       => 'derive.orders.allocation.save',
                ],
                '/orders/voucher/[order]'                    => [
                    'controller' => Vouchers::class,
                    'view'       => 'preview',
                    'name'       => 'derive.orders.voucher.preview',
                    'resolvers'  => [
                        'order' => OrdersResolver::class,
                    ],
                ],
                '/orders/summary'                            => [
                    'controller' => OrdersController::class,
                    'view'       => 'summary',
                    'name'       => 'derive.orders.summary',
                ],
                '/orders/furs'                               => [
                    'controller' => OrdersController::class,
                    'view'       => 'furs',
                    'name'       => 'derive.orders.furs',
                ],
                '/orders/furs/request-confirmation'          => [
                    'controller' => OrdersController::class,
                    'view'       => 'fursRequest',
                    'name'       => 'derive.orders.fursRequest',
                ],
                '/orders/checkin'                            => [
                    'controller' => Vouchers::class,
                    'view'       => 'checkin',
                    'name'       => 'derive.orders.checkin',
                ],
                '/orders/vouchers'                           => [
                    'controller' => Vouchers::class,
                    'view'       => 'index',
                    'name'       => 'derive.orders.vouchers',
                ],
                '/orders/voucher/generate/[orders]'          => [
                    'controller' => Vouchers::class,
                    'view'       => 'generate',
                    'name'       => 'derive.orders.vouchers.generate',
                ],
                '/orders/voucher/send/[orders]'              => [
                    'controller' => Vouchers::class,
                    'view'       => 'send',
                    'name'       => 'derive.orders.vouchers.send',
                ],
                '/orders/voucher/download/[order]'           => [
                    'controller' => Vouchers::class,
                    'view'       => 'download',
                    'name'       => 'derive.orders.voucher.download',
                    'resolvers'  => [
                        'order' => OrdersResolver::class,
                    ],
                ],
                '/orders/voucher/apply/[order]'              => [
                    'controller' => Vouchers::class,
                    'view'       => 'apply',
                    'name'       => 'derive.orders.voucher.apply',
                    'resolvers'  => [
                        'order' => OrdersResolver::class,
                    ],
                ],
                '/orders/voucher/reapply/[order]'              => [
                    'controller' => Vouchers::class,
                    'view'       => 'reapply',
                    'name'       => 'derive.orders.voucher.reapply',
                    'resolvers'  => [
                        'order' => OrdersResolver::class,
                    ],
                ],
            ],
        ];
    }

    public function paths() {
        return $this->getViewPaths();
    }

    public function consoles() {
        return [
            GenerateVoucher::class,
            SendVoucher::class,
        ];
    }

}