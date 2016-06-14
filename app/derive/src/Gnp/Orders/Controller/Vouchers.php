<?php namespace Gnp\Orders\Controller;

use Gnp\Orders\Record\Order;
use Pckg\Framework\Controller;

class Vouchers extends Controller
{

    public function getHtmlAction(Order $order) {
        return view(
            'voucher/voucher',
            [
                'order' => $order,
            ]
        );
    }

}