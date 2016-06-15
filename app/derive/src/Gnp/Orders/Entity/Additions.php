<?php namespace Gnp\Orders\Entity;

use Gnp\Orders\Record\Addition;
use Pckg\Database\Entity;
use Pckg\Database\Repository;

class Additions extends Entity
{

    protected $repositoryName = Repository::class . '.gnp';

    protected $record = Addition::class;

}