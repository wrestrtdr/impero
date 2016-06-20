<?php namespace Gnp\Orders\Entity;

use Gnp\Orders\Record\OrdersTag;
use Pckg\Database\Entity;
use Pckg\Database\Repository;

class OrdersTags extends Entity
{

    protected $record = OrdersTag::class;

    protected $repositoryName = Repository::class . '.gnp';

    public function order() {
        return $this->belongsTo(Orders::class)
                    ->foreignKey('order_id')
                    ->fill('order');
    }

}