<?php namespace Derive\Orders\Entity;

use Derive\Offers\Entity\Additions;
use Derive\Orders\Record\OrdersUsersAddition;
use Pckg\Database\Entity;
use Pckg\Database\Repository;

class OrdersUsersAdditions extends Entity
{

    protected $repositoryName = Repository::class . '.gnp';

    protected $record = OrdersUsersAddition::class;

    public function addition()
    {
        return $this->belongsTo(Additions::class)
            ->foreignKey('addition_id');
    }

}