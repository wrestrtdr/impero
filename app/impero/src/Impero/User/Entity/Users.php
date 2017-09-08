<?php namespace Impero\User\Entity;

use Impero\User\Record\User;
use Pckg\Database\Entity;

class Users extends Entity
{

    protected $record = User::class;

}