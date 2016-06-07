<?php namespace Gnp\Orders\Entity;

use Gnp\Orders\Record\Offer;
use Gnp\Orders\Record\Packet;
use Pckg\Database\Entity;
use Pckg\Database\Repository;

class Packets extends Entity
{

    protected $record = Packet::class;

    protected $repositoryName = Repository::class . '.gnp';

}