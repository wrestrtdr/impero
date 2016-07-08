<?php namespace Derive\Orders\Entity;

use Derive\Orders\Record\OrdersBill;
use Pckg\Database\Entity;
use Pckg\Database\Repository;

class OrdersBills extends Entity
{

    protected $record = OrdersBill::class;

    protected $repositoryName = Repository::class . '.gnp';

    public function order()
    {
        return $this->belongsTo(Orders::class)
                    ->foreignKey('order_id')
                    ->fill('order');
    }

}