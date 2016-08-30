<?php namespace Derive\Offers\Record;

use Derive\Offers\Entity\Cities;
use Pckg\Database\Record;

class City extends Record
{

    protected $entity = Cities::class;

    protected $toArray = ['pivot'];

}