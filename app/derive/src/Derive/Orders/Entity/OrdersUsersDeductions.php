<?php namespace Derive\Orders\Entity;

use Derive\Offers\Entity\Additions;
use Derive\Orders\Record\OrdersUsersDeduction;
use Pckg\Database\Entity;
use Pckg\Database\Repository;

class OrdersUsersDeductions extends Entity
{

    protected $repositoryName = Repository::class . '.gnp';

    protected $record = OrdersUsersDeduction::class;

    public function addition()
    {
        return $this->belongsTo(Additions::class)
                    ->foreignKey('addition_id');
    }

}