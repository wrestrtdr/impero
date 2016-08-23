<?php namespace Derive\Offers\Record;

use Carbon\Carbon;
use Derive\Offers\Entity\Offers;
use Derive\Offers\Entity\Packets;
use Pckg\Database\Record;

class Offer extends Record
{

    protected $entity = Offers::class;

    public function getUrl()
    {
        return '#';
    }

    public function getPrice()
    {
        return 123;
    }

    public function getLocation()
    {
        return $this->city->country->title . ', ' . $this->city->title;
    }

    public function hasEnabledPortions()
    {
        $pt = $this->paymentMethods;

        return true;
        (!$pt->braintreeportions && !$pt->paypalportions && !$pt->monetaportions && !$pt->upnportions) ? false : true;
    }

    public function getMaxPortions()
    {
        if (!$this->hasEnabledPortions()) {
            return 1;
        }
        
        /**
         * @T00D00
         */
    }

    public function getAvailablePacketsByType($ticket)
    {
        return (new Packets())->available()
                              ->where('offer_id', $this->id)
                              ->where('ticket', $ticket)
                              ->all();
    }

    public function getDate()
    {
        $dtLeave = $this->dt_start;
        $dtReturn = $this->dt_end;
        $date = null;

        if (date("d.m.Y", strtotime($dtLeave)) != date("d.m.Y", strtotime($dtReturn))) {
            $date .= date("j. ", strtotime($dtLeave));
            $date .= (date("m", strtotime($dtLeave)));
            $date .= ". - ";
        }
        $date .= date("j. ", strtotime($dtReturn));
        $date .= (date("m", strtotime($dtReturn)));
        $date .= date(". Y", strtotime($dtReturn));

        return $date;
    }

    public function getImage()
    {
        return '';
    }

}