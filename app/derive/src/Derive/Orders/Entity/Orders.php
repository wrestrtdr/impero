<?php namespace Derive\Orders\Entity;

use Derive\Offers\Entity\Offers;
use Derive\Offers\Entity\Packets;
use Derive\Orders\Record\Order;
use Pckg\Database\Entity;
use Pckg\Database\Entity\Extension\Paginatable;
use Pckg\Database\Query;
use Pckg\Database\Query\Raw;
use Pckg\Database\Relation\HasAndBelongsTo;
use Pckg\Database\Relation\HasMany;
use Pckg\Database\Repository;
use Pckg\Dynamic\Entity\Snippet\EntityActions;
use Pckg\Furs\Entity\Furs;
use Pckg\Maestro\Service\Contract\Entity as MaestroEntity;

class Orders extends Entity implements MaestroEntity
{

    use EntityActions, Paginatable;

    protected $record = Order::class;

    protected $repositoryName = Repository::class . '.gnp';

    public function offer()
    {
        return $this->belongsTo(Offers::class)
                    ->foreignKey('offer_id')
                    ->fill('offer');
    }

    public function activeOffer()
    {
        return $this->offer()
                    ->where('offers.dt_published')
                    ->innerJoin();
    }

    public function user()
    {
        return $this->hasOne(Users::class)
                    ->foreignKey('id')
                    ->primaryKey('user_id')
                    ->fill('user');
    }

    public function ordersUsers()
    {
        return $this->hasMany(OrdersUsers::class)
                    ->foreignKey('order_id')
                    ->fill('ordersUsers');
    }

    public function appartment()
    {
        return $this->hasOne(OrdersTags::class, 'appartment')
                    ->foreignKey('order_id')
                    ->where('appartment.type', 'appartment')
                    ->fill('appartment')
                    ->addSelect(['appartment' => 'appartment.value'])
                    ->leftJoin();
    }

    public function checkin()
    {
        return $this->hasOne(OrdersTags::class, 'checkin')
                    ->foreignKey('order_id')
                    ->where('checkin.type', 'checkin')
                    ->fill('checkin')
                    ->addSelect(['checkin' => 'checkin.value'])
                    ->leftJoin();
    }

    public function people()
    {
        return $this->hasOne(OrdersTags::class, 'people')
                    ->foreignKey('order_id')
                    ->where('people.type', 'people')
                    ->fill('people')
                    ->addSelect(['people' => 'people.value'])
                    ->leftJoin();
    }

    public function furs()
    {
        return $this->hasOne(Furs::class, 'furs')
                    ->foreignKey('order_id')
                    ->where('business_id', 'PP1')
                    ->where('business_tax_number', '81835078')
                    ->fill('furs')
                    ->addSelect(['furs.*'])
                    ->leftJoin();
    }

    public function confirmed()
    {
        return $this->where('orders.dt_confirmed');
    }

    public function payed()
    {
        return $this->where('orders.dt_payed');
    }

    /**
     * @return HasAndBelongsTo
     */
    public function packets()
    {
        return $this->hasAndBelongsTo(Packets::class)
                    ->over(OrdersUsers::class)
                    ->leftForeignKey('order_id')
                    ->rightForeignKey('packet_id')
                    ->leftPrimaryKey('id')
                    ->rightPrimaryKey('id')
                    ->fill('packets');
    }

    public function ordersBills()
    {
        return $this->hasMany(OrdersBills::class)
                    ->foreignKey('order_id')
                    ->fill('ordersBills');
    }

    public function confirmedPackets()
    {
        $relation = $this->packets();

        $relation->getMiddleEntity()->where('orders_users.dt_confirmed');

        $relation->fill('confirmedPackets');

        return $relation;
    }

    public function forOrders()
    {
        return $this->joinCheckin()
                    ->joinAppartment()
                    ->joinPeople()
                    ->withOffer();
    }

    public function forAllocation()
    {
        return $this->payed()
                    ->confirmed()
                    ->withUser()
                    ->withOrdersUsers(
                        function(HasMany $ordersUsers) {
                            $ordersUsers->where('orders_users.dt_confirmed');
                            $ordersUsers->withPacket();
                        }
                    );
    }

    public function forVouchers()
    {
        return $this->payed()
                    ->confirmed()
                    ->withOffer()
                    ->joinCheckin()
                    ->joinAppartment()
                    ->joinPeople()
                    ->withUser()
                    ->withOrdersUsers(
                        function(HasMany $ordersUsers) {
                            $ordersUsers->where('orders_users.dt_confirmed');
                            $ordersUsers->withPacket();
                        }
                    );
    }

    public function forSummary()
    {
        return $this->joinOffer()
                    ->joinOrdersBills(
                        function(Query $ordersBills) {
                            $ordersBills->where('type', [1, 2]);
                        }
                    );
    }

    public function forFurs()
    {
        return $this->where(Raw::raw('orders.id IN(SELECT order_id FROM orders_bills WHERE payed AND type IN(1,2))'))
                    ->withOffer()
                    ->withUser()
                    ->withOrdersBills(
                        function(HasMany $ordersBills) {
                            $ordersBills->where('type', [1, 2]);
                        }
                    )->withFurs();
    }

    public function forCheckin()
    {
        return $this->payed()
                    ->confirmed()
                    ->withOffer()
                    ->withUser()
                    ->withOrdersUsers(
                        function(HasMany $ordersUser) {
                            $ordersUser->withAdditions();
                            $ordersUser->withPacket();
                        }
                    );
    }

}