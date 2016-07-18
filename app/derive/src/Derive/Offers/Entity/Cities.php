<?php namespace Derive\Offers\Entity;

use Pckg\Database\Entity;
use Pckg\Database\Repository;

class Cities extends Entity
{

    protected $repositoryName = Repository::class . '.gnp';

    public function country()
    {
        return $this->belongsTo(Countries::class)
                    ->foreignKey('country_id');
    }

}