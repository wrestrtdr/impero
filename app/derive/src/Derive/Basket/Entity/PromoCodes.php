<?php namespace Derive\Basket\Entity;

use Derive\Basket\Record\PromoCode;
use Derive\Offers\Entity\Offers;
use Pckg\Database\Entity;
use Pckg\Database\Repository;

class PromoCodes extends Entity
{

    protected $repositoryName = Repository::class . '.gnp';

    protected $record = PromoCode::class;

    public function offers() {
        return $this->hasAndBelongsTo(Offers::class)
            ->over('offers_promo_codes')
            ->leftForeignKey('promo_code_id')
            ->rightForeignKey('offer_id');
    }

}