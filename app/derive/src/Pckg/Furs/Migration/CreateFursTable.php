<?php namespace Pckg\Furs\Migration;

use Pckg\Migration\Migration;

class CreateFursTable extends Migration
{

    public function up()
    {
        $furs = $this->table('furs', false, false);
        $furs->integer('order_id');
        $furs->varchar('business_id');
        $furs->varchar('business_tax_number');
        $furs->datetime('requested_at')->nullable();

        $this->save();
    }

}