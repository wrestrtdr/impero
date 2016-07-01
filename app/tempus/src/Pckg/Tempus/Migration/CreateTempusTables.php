<?php namespace Pckg\Tempus\Migration;

use Pckg\Migration\Migration;

class CreateTempusTables extends Migration
{

    public function up() {
        $items = $this->table('items');
        $items->varchar('program');
        $items->varchar('name');
        $items->varchar('role');
        $items->integer('idle');
        $items->datetime('created_at');

        $this->save();
    }

}