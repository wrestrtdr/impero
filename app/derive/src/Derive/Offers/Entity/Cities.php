<?php namespace Derive\Offers\Entity;

use Derive\Offers\Record\City;
use Pckg\Database\Entity;
use Pckg\Database\Repository;

class Cities extends Entity
{

    protected $repositoryName = Repository::class . '.gnp';

    protected $record = City::class;

    public function country()
    {
        return $this->belongsTo(Countries::class)
                    ->foreignKey('country_id');
    }

}