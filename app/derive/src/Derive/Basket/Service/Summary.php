<?php namespace Derive\Basket\Service;

use Derive\Basket\Service\Summary\Item;
use Derive\Orders\Record\Order;
use JsonSerializable;
use Pckg\Collection;

class Summary implements JsonSerializable
{

    protected $items = [];

    public function addItem(Item $item)
    {
        $this->items[] = $item;

        return $this;
    }

    public function getSum()
    {
        $sum = 0.0;
        (new Collection($this->items))->each(
            function(Item $item) use (&$sum) {
                $sum += $item->getTotal();
            }
        );

        return $sum;
    }

    public function getProcessingCost()
    {
        $min = config('defaults.derive.basket.processingCost.min');
        $max = config('defaults.derive.basket.processingCost.max');
        $ratio = config('defaults.derive.basket.processingCost.ratio');

        $sum = $this->getSum();
        $cost = $sum * $ratio;

        if ($cost < $min) {
            return $min;

        } else if ($cost > $max) {
            return $max;

        } else {
            return $cost;

        }
    }

    public function getInstallments(Order $order, $num = 1)
    {
        $dueDates = $order->getInstallments($num);

        $sum = 0.0;
        foreach ($dueDates as $i => &$dueDate) {
            if ($i + 1 < count($dueDates)) {
                $price = round($order->price / count($dueDates), 2);
            } else {
                $price = round($order->price - $sum, 2);
            }
            $dueDate = [
                'num'     => $i + 1,
                'dueDate' => $dueDate,
                'price'   => $price,
            ];
            $sum += $price;
        }

        return $dueDates;
    }

    public function getPortions($order, $num = 1)
    {
        return $this->getInstallments($order, $num);
    }

    function jsonSerialize()
    {
        return [
            'items' => $this->items,
            'sum'   => $this->getSum(),
        ];
    }
}