<?php namespace Derive\Basket\Service\Summary\Item;

use Derive\Basket\Service\Summary\Item;
use Derive\Offers\Record\Packet as PacketRecord;
use JsonSerializable;

class Packet implements Item, JsonSerializable
{

    use Shared;

    /**
     * @var PacketRecord
     */
    protected $packet;

    protected $quantity;

    public function __construct(PacketRecord $packet, $quantity = 1)
    {
        $this->packet = $packet;
        $this->quantity = $quantity;
    }

    public function getTitle()
    {
        return $this->packet->title;
    }

    public function getPrice()
    {
        return $this->packet->price;
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