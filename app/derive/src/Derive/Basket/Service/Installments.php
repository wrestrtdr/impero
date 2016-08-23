<?php namespace Derive\Basket\Service;

use Carbon\Carbon;
use Derive\Orders\Record\Order;
use Derive\Orders\Record\OrdersBill;

class Installments
{

    /**
     * @Order
     */
    protected $order;

    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }

}