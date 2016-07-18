<?php namespace Derive\Offers\Record;

use Derive\Offers\Entity\Offers;
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

    public function getImage() {
        return '';
    }

}