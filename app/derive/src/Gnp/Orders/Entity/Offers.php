<?php namespace Gnp\Orders\Entity;

use Gnp\Orders\Record\Offer;
use Pckg\Database\Entity;
use Pckg\Database\Repository;

class Offers extends Entity
{

    protected $record = Offer::class;

    protected $repositoryName = Repository::class . '.gnp';

}