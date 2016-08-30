<?php namespace Derive\Basket\Service\Summary\Item;

use Derive\Basket\Service\Summary\Item;
use Derive\Offers\Record\Addition as AdditionRecord;
use Derive\Offers\Record\Packet as PacketRecord;
use JsonSerializable;

class Addition implements Item, JsonSerializable
{

    use Shared;

    /**
     * @var PacketRecord
     */
    protected $addition;

    protected $quantity;

    public function __construct(AdditionRecord $addition, $quantity = 1)
    {
        $this->addition = $addition;
        $this->quantity = $quantity;
    }

    public function getTitle()
    {
        return $this->addition->title;
    }

    public function getPrice()
    {
        return $this->addition->value;
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