<?php namespace Gnp\Orders\Record;

use Gnp\Orders\Entity\OrdersUsers;
use Pckg\Database\Record;
use Pckg\Database\Repository;

class OrdersUser extends Record
{

    protected $entity = OrdersUsers::class;

    protected $toArray = ['groupedPackets'];

}