<?php namespace Derive\Offers\Entity;

use Derive\Offers\Record\Offer;
use Pckg\Database\Entity;
use Pckg\Database\Query\Raw;
use Pckg\Database\Relation\BelongsTo;
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

    public function forHomepage()
    {
        return $this->published()
                    ->onFirstPage()
                    ->withCity(
                        function(BelongsTo $city) {
                            $city->withCountry();
                        }
                    )
                    ->withCategory()
                    ->withPackets(
                        function(HasMany $packets) {
                            $packets->published();
                        }
                    );
    }

    public function forListing()
    {
        return $this->published()
                    ->onFirstPage()
                    ->withCity(
                        function(BelongsTo $city) {
                            $city->withCountry();
                        }
                    )
                    ->withCategory()
                    ->count()
                    ->orderBy('offers.top DESC, offers.position_index ASC');
    }

    public function city()
    {
        return $this->belongsTo(Cities::class)
                    ->foreignKey('city_id');
    }

    public function category()
    {
        return $this->belongsTo(Categories::class)
                    ->foreignKey('category_id');
    }

    public function published()
    {
        return $this->where(Raw::raw('offers.dt_published'));
    }

    public function onFirstPage()
    {
        return $this->where(Raw::raw('offers.firstpage'));
    }

    public function packets()
    {
        return $this->hasMany(Packets::class)
                    ->foreignKey('offer_id')
                    ->fill('packets');
    }

}