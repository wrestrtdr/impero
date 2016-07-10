<?php namespace Derive\Orders\Entity;

use Derive\Orders\Record\Offer;
use Pckg\Database\Entity;
use Pckg\Database\Relation\HasMany;
use Pckg\Database\Repository;

class Offers extends Entity
{

    protected $record = Offer::class;

    protected $repositoryName = Repository::class . '.gnp';

    public function forOrderForm()
    {
        return $this->published()->withPackets(
            function(HasMany $packets) {
                $packets->forOrderForm();
            }
        );
    }

}