<?php namespace Pckg\Furs\Service\Furs;

class Business
{

    protected $id;

    protected $taxNumber;

    protected $validityDate;

    protected $electronicDeviceId;

    public function __construct($id, $taxNumber, $validityDate, $electronicDeviceId)
    {
        $this->id = $id;
        $this->taxNumber = $taxNumber;
        $this->validityDate = $validityDate;
        $this->electronicDeviceId = $electronicDeviceId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTaxNumber()
    {
        return $this->taxNumber;
    }

    public function getValidityDate()
    {
        return $this->validityDate;
    }

    public function getElectronicDeviceId()
    {
        return $this->electronicDeviceId;
    }

}