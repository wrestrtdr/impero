<?php namespace Impero\Mysql\Entity;

use Pckg\Maestro\Service\Contract\Entity as MaestroEntity;
use Impero\Mysql\Record\Database;
use Pckg\Database\Entity;

class Databases extends Entity implements MaestroEntity
{

    protected $record = Database::class;

    /**
     * Build edit url.
     *
     * @return string
     */
    public function getAddUrl()
    {
        return url('database.add');
    }

}