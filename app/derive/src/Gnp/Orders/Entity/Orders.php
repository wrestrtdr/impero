<?php namespace Gnp\Orders\Entity;

use Gnp\Orders\Record\Order;
use Pckg\Database\Entity;
use Pckg\Database\Query\Raw;
use Pckg\Database\Relation\HasAndBelongsTo;
use Pckg\Database\Repository;
use Pckg\Dynamic\Entity\Snippet\EntityActions;
use Pckg\Maestro\Service\Contract\Entity as MaestroEntity;

class Orders extends Entity implements MaestroEntity
{

    use EntityActions;

    protected $record = Order::class;

    protected $repositoryName = Repository::class . '.gnp';

    public function offer() {
        return $this->belongsTo(Offers::class)
                    ->foreignKey('offer_id')
                    ->primaryKey('id')
                    ->fill('offer');
    }

    public function activeOffer() {
        return $this->offer()
                    ->where('offers.dt_published')
                    ->innerJoin();
    }

    public function user() {
        return $this->hasOne(Users::class)
                    ->foreignKey('id')
                    ->primaryKey('user_id')
                    ->fill('user');
    }

    public function ordersUsers() {
        return $this->hasMany(OrdersUsers::class)
                    ->foreignKey('order_id')
                    ->primaryKey('id')
                    ->fill('ordersUsers');
    }

    public function appartment() {
        return $this->hasOne(OrdersTags::class)
                    ->foreignKey('order_id')
                    ->primaryKey('id')
                    ->where('type', 'appartment')
                    ->fill('appartment');
    }

    public function checkin() {
        return $this->hasOne(OrdersTags::class)
                    ->foreignKey('order_id')
                    ->primaryKey('id')
                    ->where('type', 'checkin')
                    ->fill('checkin');
    }

    public function people() {
        return $this->hasOne(OrdersTags::class)
                    ->foreignKey('order_id')
                    ->primaryKey('id')
                    ->where('type', 'people')
                    ->fill('people');
    }

    public function getAddUrl() {
        return '#';
    }

    public function confirmed() {
        return $this->where('dt_confirmed');
    }

    public function payed() {
        return $this->where('dt_payed');
    }

    /**
     * @return HasAndBelongsTo
     */
    public function packets() {
        return $this->hasAndBelongsTo(Packets::class)
                    ->over(OrdersUsers::class)
                    ->leftForeignKey('order_id')
                    ->rightForeignKey('packet_id')
                    ->leftPrimaryKey('id')
                    ->rightPrimaryKey('id')
                    ->fill('packets');
    }

    public function confirmedPackets() {
        $relation = $this->packets();

        $relation->getMiddleEntity()->where('orders_users.dt_confirmed');

        return $relation;
    }

    public function forAllocation() {
        return $this->payed()
            ->confirmed()
            ->withUser()
            ->withConfirmedPackets()
            ->where('offer_id', [17, 18, 19]);
    }

}