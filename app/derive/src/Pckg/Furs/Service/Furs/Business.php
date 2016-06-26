<?php namespace Pckg\Furs\Service\Furs;

class Business
{

    protected $id;

    protected $taxNumber;

    protected $validityDate;

    public function __construct($id, $taxNumber, $validityDate) {
        $this->id = $id;
        $this->taxNumber = $taxNumber;
        $this->validityDate = $validityDate;
    }

    public function getId() {
        return $this->id;
    }

    public function getTaxNumber() {
        return $this->taxNumber;
    }

    public function getValidityDate() {
        return $this->validityDate;
    }

}