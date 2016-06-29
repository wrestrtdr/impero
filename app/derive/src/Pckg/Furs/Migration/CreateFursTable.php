<?php namespace Pckg\Furs\Migration;

use Pckg\Migration\Migration;

class CreateFursTable extends Migration
{

    public function up() {
        $furs = $this->table('furs');
        $furs->integer('order_id');
        $furs->integer('platform_id')->references('platforms');
        $furs->datetime('requested_at')->nullable();
        
        $this->save();
    }

}