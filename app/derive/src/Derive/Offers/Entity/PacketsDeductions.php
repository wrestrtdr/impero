<?php namespace Derive\Offers\Entity;

use Pckg\Database\Entity;
use Pckg\Database\Repository;

class PacketsDeductions extends Entity
{

    protected $repositoryName = Repository::class . '.gnp';

    public function addition()
    {
        return $this->belongsTo(Additions::class)
                    ->foreignKey('addition_id')
                    ->fill('addition');
    }

}