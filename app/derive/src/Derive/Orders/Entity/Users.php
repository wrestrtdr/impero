<?php namespace Derive\Orders\Entity;

use Derive\Orders\Record\User;
use Pckg\Database\Entity;
use Pckg\Database\Repository;

class Users extends Entity
{

    protected $record = User::class;

    protected $repositoryName = Repository::class . '.gnp';

}