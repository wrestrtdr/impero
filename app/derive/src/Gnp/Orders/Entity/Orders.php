<?php namespace Gnp\Orders\Entity;

use Gnp\Orders\Record\Order;
use Pckg\Database\Entity;
use Pckg\Database\Repository;
use Pckg\Maestro\Service\Contract\Entity as MaestroEntity;

class Orders extends Entity implements MaestroEntity
{

    protected $record = Order::class;

    protected $repositoryName = Repository::class . '.gnp';

    public function offer() {
        return $this->belongsTo(Offers::class)
                    ->foreignKey('offer_id')
                    ->primaryKey('id')
                    ->fill('offer');
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
                    ->addCondition('type = \'appartment\'')
                    ->fill('appartment');
    }

    public function checkin() {
        return $this->hasOne(OrdersTags::class)
                    ->foreignKey('order_id')
                    ->primaryKey('id')
                    ->addCondition('type = \'checkin\'')
                    ->fill('checkin');
    }

    public function people() {
        return $this->hasOne(OrdersTags::class)
                    ->foreignKey('order_id')
                    ->primaryKey('id')
                    ->addCondition('type = \'people\'')
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

}