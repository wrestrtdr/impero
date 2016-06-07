<?php namespace Gnp\Orders\Record;

use Gnp\Orders\Entity\Orders;
use Gnp\Orders\Entity\OrdersTags;
use Pckg\Database\Record;

class Order extends Record
{

    protected $entity = Orders::class;

    public function getEditUrl() {
        return '#';
    }

    public function getDeleteUrl() {
        return '#';
    }

    public function setAppartment($appartment) {
        $this->setTag('appartment', $appartment);
    }

    public function setCheckin($checkin) {
        $this->setTag('checkin', $checkin);

    }

    public function setPeople($people) {
        $this->setTag('people', $people);

    }

    protected function setTag($type, $value) {
        $attr = (
        new OrdersTag(
            [
                'type'     => $type,
                'order_id' => $this->id,
            ]
        )
        )->refetch();

        $attr->value = $value;
        $attr->save();
    }

}