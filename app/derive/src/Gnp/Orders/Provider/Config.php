<?php namespace Gnp\Orders\Provider;

use Gnp\Orders\Console\GenerateVoucher;
use Gnp\Orders\Console\SendVoucher;
use Gnp\Orders\Controller\Orders;
use Gnp\Orders\Controller\Vouchers;
use Gnp\Orders\Resolver\Orders as OrdersResolver;
use Pckg\Framework\Provider;

class Config extends Provider
{

    public function routes() {
        return [
            'url' => [
                '/orders'                                    => [
                    'controller' => Orders::class,
                    'view'       => 'group',
                    'name'       => 'derive.orders.group',
                ],
                '/orders/allocation/[order]'                 => [
                    'controller' => Orders::class,
                    'view'       => 'allocation',
                    'name'       => 'derive.orders.allocation.get',
                    'resolvers'  => [
                        'order' => OrdersResolver::class,
                    ],
                ],
                '/orders/allocation/attributes/[appartment]' => [
                    'controller' => Orders::class,
                    'view'       => 'allocationAttributes',
                    'name'       => 'derive.orders.allocation.attributes',
                ],
                '/orders/allocation/non-allocated'           => [
                    'controller' => Orders::class,
                    'view'       => 'allocationNonAllocated',
                    'name'       => 'derive.orders.allocation.nonallocated',
                ],
                '/orders/allocation/similar'                 => [
                    'controller' => Orders::class,
                    'view'       => 'allocationSimilar',
                    'name'       => 'derive.orders.allocation.similar',
                ],
                '/orders/allocation/save'                    => [
                    'controller' => Orders::class,
                    'view'       => 'save',
                    'name'       => 'derive.orders.allocation.save',
                ],
                '/orders/voucher/[order]'                    => [
                    'controller' => Vouchers::class,
                    'view'       => 'html',
                    'name'       => 'derive.orders.voucher.getHtml',
                    'resolvers'  => [
                        'order' => OrdersResolver::class,
                    ],
                ],
                '/orders/vouchers'                           => [
                    'controller' => Vouchers::class,
                    'view'       => 'index',
                    'name'       => 'derive.orders.vouchers',
                ],
                '/orders/voucher/generate/[orders]'         => [
                    'controller' => Vouchers::class,
                    'view'       => 'generate',
                    'name'       => 'derive.orders.vouchers.generate',
                ],
                '/orders/voucher/send/[orders]'             => [
                    'controller' => Vouchers::class,
                    'view'       => 'send',
                    'name'       => 'derive.orders.vouchers.send',
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