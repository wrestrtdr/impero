<?php namespace Derive\Basket\Service\Summary\Item;

class Item implements \Derive\Basket\Service\Summary\Item, \JsonSerializable
{

    use Shared;

    protected $title;

    protected $price;

    protected $quantity;

    public function __construct($title, $price, $quantity)
    {
        $this->title = $title;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getTotal()
    {
        return $this->getQuantity() * $this->getPrice();
    }
}