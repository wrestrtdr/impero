<?php namespace Derive\Orders\Entity;

use Pckg\Database\Entity;
use Pckg\Database\Repository;

class PacketsAdditions extends Entity
{

    protected $repositoryName = Repository::class . '.gnp';

    public function addition()
    {
        return $this->belongsTo(Additions::class)
                    ->foreignKey('addition_id')
                    ->fill('addition');
    }

}