<?php namespace Derive\Offers\Entity;

use Derive\Offers\Record\Packet;
use Pckg\Database\Entity;
use Pckg\Database\Relation\HasMany;
use Pckg\Database\Repository;

class Packets extends Entity
{

    protected $record = Packet::class;

    protected $repositoryName = Repository::class . '.gnp';

    public function voucherTab()
    {
        return $this->hasOne(PacketsTabs::class)
                    ->foreignKey('packet_id')
                    ->fill('voucherTab')
                    ->where('picture', null, 'IS NOT');
    }

    public function forOrderForm()
    {
        return $this->published()
                    ->available()
                    ->withAdditions(
                        function(HasMany $additions) {
                            $additions->published();
                            $additions->available();
                        }
                    );
    }

    public function forSecondStep()
    {
        return $this->published()
                    ->available()
                    ->withAdditions(
                        function(HasMany $additions) {
                            $additions->published()
                                      ->available();
                        }
                    )
                    ->withDepartments()
                    ->withIncludes(
                        function(HasMany $includes) {
                            $includes->published();
                        }
                    )
                    ->withDeductions(
                        function(HasMany $deductions) {
                            $deductions->published();
                        }
                    );
    }

    public function published()
    {
        return $this->where('dt_published');
    }

    public function available()
    {
        return $this;
    }

    public function additions()
    {
        return $this->hasAndBelongsTo(Additions::class)
                    ->over(PacketsAdditions::class)
                    ->leftForeignKey('packet_id')
                    ->rightForeignKey('addition_id');
    }

    public function departments()
    {
        return $this->hasAndBelongsTo(Cities::class)
                    ->over(PacketsCities::class)
                    ->leftForeignKey('packet_id')
                    ->rightForeignKey('city_id');
    }

    public function includes()
    {
        return $this->hasAndBelongsTo(Additions::class)
                    ->over(PacketsIncludes::class)
                    ->leftForeignKey('packet_id')
                    ->rightForeignKey('addition_id');
    }

    public function deductions()
    {
        return $this->hasAndBelongsTo(Additions::class)
                    ->over(PacketsDeductions::class)
                    ->leftForeignKey('packet_id')
                    ->rightForeignKey('deduction_id');
    }

}