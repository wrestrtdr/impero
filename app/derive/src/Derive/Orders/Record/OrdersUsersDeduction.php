<?php namespace Derive\Orders\Record;

use Derive\Orders\Entity\OrdersUsers;
use Derive\Orders\Entity\OrdersUsersAdditions;
use Derive\Orders\Entity\OrdersUsersDeductions;
use Pckg\Database\Record;
use Pckg\Database\Repository;

class OrdersUsersDeduction extends Record
{

    protected $entity = OrdersUsersDeductions::class;

}