<?php namespace Impero\Mysql\Entity;

use Pckg\Maestro\Service\Contract\Entity as MaestroEntity;
use Impero\Mysql\Record\Database;
use Impero\Mysql\Record\User;
use Pckg\Database\Entity;

class Users extends Entity implements MaestroEntity
{

    protected $record = User::class;

    protected $table = 'database_users';

    /**
     * Build edit url.
     *
     * @return string
     */
    public function getAddUrl()
    {
        return url('user.add');
    }

}