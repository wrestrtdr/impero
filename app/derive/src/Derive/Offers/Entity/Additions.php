<?php namespace Derive\Offers\Entity;

use Derive\Offers\Record\Addition;
use Pckg\Database\Entity;
use Pckg\Database\Query\Raw;
use Pckg\Database\Repository;

class Additions extends Entity
{

    protected $repositoryName = Repository::class . '.gnp';

    protected $record = Addition::class;

    public function published()
    {
        return $this;
        return $this->where(Raw::raw('additions.dt_published'));
    }

    public function available()
    {
        /**
         * @T00D00 - build query for available additions
         */
        return $this;
    }

}