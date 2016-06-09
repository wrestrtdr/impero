<?php namespace Gnp\Orders\Record;

use Gnp\Orders\Entity\Orders;
use Pckg\Database\Record;

class Order extends Record
{

    protected $entity = Orders::class;

    protected $toArray = ['user', 'packetsSummary'];

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

    public function getPacketsSummary() {
        $packets = $this->packets->removeEmpty()->groupBy('id');

        return implode("<br />", array_map(function($packetGroup){
            return count($packetGroup) . 'x ' . $packetGroup[0]->title;
        }, $packets->all()));
    }

}