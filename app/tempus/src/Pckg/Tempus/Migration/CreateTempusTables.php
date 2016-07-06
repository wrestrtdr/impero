<?php namespace Pckg\Tempus\Migration;

use Pckg\Migration\Migration;

class CreateTempusTables extends Migration
{

    public function up() {
        $items = $this->table('items');
        $items->varchar('program');
        $items->varchar('name', 255);
        $items->varchar('role');
        $items->integer('idle');
        $items->datetime('created_at');
        $items->datetime('finished_at');
        $items->integer('duration');

        $this->save();
    }

}