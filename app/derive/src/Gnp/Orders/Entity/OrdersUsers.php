<?php namespace Gnp\Orders\Entity;

use Gnp\Orders\Record\OrdersUser;
use Pckg\Database\Entity;
use Pckg\Database\Repository;

class OrdersUsers extends Entity
{

    protected $record = OrdersUser::class;

    protected $repositoryName = Repository::class . '.gnp';

    public function order() {
        return $this->belongsTo(Orders::class)
                    ->foreignKey('order_id')
                    ->primaryKey('id')
                    ->fill('order');
    }

    public function packet() {
        return $this->belongsTo(Packets::class)
                    ->foreignKey('packet_id')
                    ->primaryKey('id')
                    ->fill('packet');
    }

    public function groupedPackets() {
        return $this->packet()
                    ->fill('groupedPackets')
                    ->groupBy('packets.id')
                    ->addSelect(['total' => 'COUNT(id)']);
    }

    public function confirmed() {
        return $this->where('dt_confirmed');
    }

    public function additions() {
        return $this->hasAndBelongsTo(PacketsAdditions::class)
                    ->over(OrdersUsersAdditions::class)
                    ->leftForeignKey('orders_user_id')
                    ->rightForeignKey('addition_id')
                    ->leftPrimaryKey('id')
                    ->rightPrimaryKey('id')
                    ->fill('additions');
    }

}