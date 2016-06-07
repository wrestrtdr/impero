<?php namespace Gnp\Orders\Migration;

use Pckg\Migration\Migration;

class OrdersTags extends Migration
{

    public function up() {
        $ordersUsersTags = $this->table('orders_tags');
        $ordersUsersTags->integer('order_id')->references('orders');
        $ordersUsersTags->varchar('type');
        $ordersUsersTags->varchar('value');

        $this->save();
    }

}