<?php namespace Gnp\Orders\Entity;

use Gnp\Orders\Record\Offer;
use Gnp\Orders\Record\User;
use Pckg\Database\Entity;
use Pckg\Database\Repository;

class Users extends Entity
{

    protected $record = User::class;

    protected $repositoryName = Repository::class . '.gnp';

}