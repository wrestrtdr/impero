<?php namespace Derive\Basket\Service\Summary\Item;

use Derive\Basket\Service\Summary\Item;
use Derive\Offers\Record\Packet as PacketRecord;

class Deduction implements Item
{

    /**
     * @var PacketRecord
     */
    protected $deduction;

    protected $quantity;

    public function __construct(Deduction $deduction, $quantity = 1)
    {
        $this->deduction = $deduction;
        $this->quantity = $quantity;
    }

    public function getTitle()
    {
        return $this->deduction->title;
    }

    public function getPrice()
    {
        return $this->deduction->value;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getTotal()
    {
        return $this->getQuantity() * $this->getPrice();
    }

}