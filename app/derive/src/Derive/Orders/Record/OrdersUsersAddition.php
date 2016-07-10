<?php namespace Derive\Orders\Record;

use Derive\Orders\Entity\OrdersUsers;
use Derive\Orders\Entity\OrdersUsersAdditions;
use Pckg\Database\Record;
use Pckg\Database\Repository;

class OrdersUsersAddition extends Record
{

    protected $entity = OrdersUsersAdditions::class;

}