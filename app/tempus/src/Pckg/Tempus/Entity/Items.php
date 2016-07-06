<?php namespace Pckg\Tempus\Entity;

use Pckg\Database\Entity;
use Pckg\Database\Query\Raw;
use Pckg\Tempus\Record\Item;

class Items extends Entity
{

    protected $record = Item::class;

    public function prevItem() {
        return $this->hasOne(Items::class, 'prev_item')
                    ->where(new Raw('prev_item.id = items.id - 1'))
                    ->leftJoin();
    }

    public function nextItem() {
        return $this->hasOne(Items::class, 'next_item')
                    ->where(new Raw('next_item.id = items.id + 1'))
                    ->leftJoin();
    }

    public function active() {
        return $this->where('idle', 2 * 60 * 1000, '<');
    }

}