<?php namespace Derive\Basket\Entity;

use Derive\Basket\Record\PromoCode;
use Pckg\Database\Entity;
use Pckg\Database\Repository;

class PromoCodes extends Entity
{

    protected $repositoryName = Repository::class . '.gnp';

    protected $record = PromoCode::class;

}