<?php namespace Impero\User\Record;

use Impero\User\Entity\Users;
use Pckg\Database\Record;

class User extends Record
{

    protected $entity = Users::class;

}