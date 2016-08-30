<?php namespace Derive\Basket\Service\Summary\Item;

trait Shared
{

    function jsonSerialize()
    {
        return [
            'title'    => $this->getTitle(),
            'price'    => $this->getPrice(),
            'quantity' => $this->getQuantity(),
            'total'    => $this->getTotal(),
        ];
    }

}