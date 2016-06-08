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
        $packets = $this->ordersUsers->each(function($ordersUser){
            return $ordersUser->packet;
        }, true)->removeEmpty()->keyBy('id');

        $quantities = [];
        foreach ($this->ordersUsers as $ordersUser) {
            if (!$packets->keyExists($ordersUser->packet_id)) {
                continue;
            }

            $packet = $packets->getKey($ordersUser->packet_id);

            if (!isset($quantities[$packet->id])) {
                $quantities[$packet->id] = 1;

            } else {
                $quantities[$packet->id]++;

            }
        }

        return implode('<br />', array_map(function($packet) use ($quantities) {
            return $quantities[$packet->id] . 'x ' . $packet->title;
        }, $packets->all()));
    }

}