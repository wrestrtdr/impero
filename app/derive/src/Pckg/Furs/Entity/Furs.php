<?php namespace Pckg\Furs\Entity;

use Derive\Orders\Record\Order;
use Pckg\Database\Entity;
use Pckg\Database\Repository;
use Pckg\Furs\Record\Furs as FursRecord;
use Pckg\Furs\Service\Furs\Business;

class Furs extends Entity
{

    protected $record = FursRecord::class;

    protected $repositoryName = Repository::class . '.deriveprod';

    public function getOrCreateFromOrder(Order $order, Business $business)
    {
        /**
         * Get existent furs record
         */
        $furs = (new static())
            ->where('order_id', $order->id)
            ->where('business_id', $business->getId())
            ->where('business_tax_number', $business->getTaxNumber())
            ->where('platform_id', $_SESSION['platform_id'])
            ->one();

        if (!$furs) {
            $last = (new static())
                ->where('business_id', $business->getId())
                ->where('business_tax_number', $business->getTaxNumber())
                ->orderBy('furs_id DESC')
                ->one();

            $furs = new FursRecord(
                [
                    'furs_id'             => $last ? $last->furs_id + 1 : 1,
                    'order_id'            => $order->id,
                    'business_id'         => $business->getId(),
                    'business_tax_number' => $business->getTaxNumber(),
                    'platform_id'         => $_SESSION['platform_id'],
                ]
            );
        }

        $furs->requested_at = date('Y-m-d H:i:s');
        $furs->save();

        return $furs;
    }

}