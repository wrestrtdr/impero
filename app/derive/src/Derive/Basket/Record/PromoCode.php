<?php namespace Derive\Basket\Record;

use Derive\Basket\Entity\PromoCodes;
use Derive\Offers\Record\Offer;
use Derive\Orders\Entity\Orders;
use Pckg\Database\Record;

class PromoCode extends Record
{

    protected $entity = PromoCodes::class;

    public function getError(Offer $offer)
    {
        /**
         * Check if promo code is activated.
         */
        if (!$this->active) {
            return __('promo_code_not_active');
        }

        /**
         * Check limitation by offer.
         */
        if ($this->offers->count() && $this->offers->keyBy('id')->hasKey($offer->id)) {
            return __('promo_code_not_activated_on_offer');
        }

        /**
         * Check limit
         */
        if ($this->limit) {
            $usedTimes = (new Orders())->confirmed()
                                       ->where('promo_code_id', $this->code)
                                       ->total();

            if ($this->limit <= $usedTimes) {
                return __('promo_code_limit_exceeded');
            }
        }

        /**
         * No error, yeeey. =)
         */
        return null;
    }

    public function applyToPrice($price)
    {
        return makePrice($price - $this->getPriceDiff($price));
    }

    public function getPriceDiff($price)
    {
        if ($this->promo_code_type_id == 2) {
            return round($price * ($this->value) / 100, 2);

        } else {
            return round($this->value, 2);

        }
    }

}