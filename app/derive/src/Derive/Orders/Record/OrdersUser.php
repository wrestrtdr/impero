<?php namespace Derive\Orders\Record;

use Derive\Orders\Entity\OrdersUsers;
use Pckg\Database\Record;
use Pckg\Database\Repository;

class OrdersUser extends Record
{

    protected $entity = OrdersUsers::class;

    protected $toArray = ['groupedPackets'];

}