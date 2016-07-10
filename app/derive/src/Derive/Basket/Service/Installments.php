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

    public function redefine($number)
    {
        /**
         * Rules:
         *  - try to match 5., 15. and 25. in the month for due date
         *  - orders made 15 days or less to the event should be payed instantly
         *  - orders made 45 days or less to the event should be payed instantly or instantly+15 days before event
         */
        $today = Carbon::now();
        $dueDate = Carbon::now();
        $eventDate = Carbon::parse($this->order->offer->dt_closed);
        $lastPayment = $eventDate->subDays(15);
        $diffInDays = $eventDate->diffInDays($today);

        /**
         * First payment is always today.
         */
        $dueDates = [$today];

        if ($diffInDays <= 15) {
            /**
             * Orders made 15 days or less to the event should be payed instantly.
             */
            return $dueDates;
        }

        /**
         * Find next 5., 15., or 25.
         */
        while ($dueDate->format('%d') % 5 != 0) {
            $dueDate->addDay(1);
        }

        for ($i = 2; $i <= $number; $i++) {
            $dueDate->addMonth();

            if ($lastPayment->lt($dueDate)) {
                $dueDates[] = $lastPayment->copy();
                break;
            }

            $dueDates[] = $dueDate->copy();
        }

        /**
         * We have due dates. Now we should make prices.
         */
        $payed = 0.0;
        foreach ($this->order->ordersBills as $bill) {
            if ($bill->payed) {
                $payed += $bill->payed;
            }
            $bill->delete();
        }

        $sum = 0.0;
        foreach ($dueDates as $i => $dueDate) {
            if ($i + 1 < count($dueDates)) {
                $price = round($this->order->total / count($dueDates), 2);
            } else {
                $price = round($this->order->total - $sum, 2);
            }
            $ordersBill = new OrdersBill(
                [
                    'order_id' => $this->order->id,
                    'valid_at' => $dueDate,
                    'price'    => $price,
                    'payed'    => $payed,
                ]
            );
            $ordersBill->save();
            $payed = 0.0;
        }
    }

}