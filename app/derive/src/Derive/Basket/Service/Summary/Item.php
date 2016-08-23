<?php namespace Derive\Basket\Service\Summary;

interface Item
{

    public function getTitle();

    public function getPrice();

    public function getQuantity();

    public function getTotal();

}