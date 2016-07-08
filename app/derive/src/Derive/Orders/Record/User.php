<?php namespace Derive\Orders\Record;

use Derive\Orders\Entity\Users;
use Pckg\Database\Record;

class User extends Record
{

    protected $entity = Users::class;

}