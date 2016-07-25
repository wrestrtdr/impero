<?php namespace Derive\Orders\Migration;

use Pckg\Database\Repository;
use Pckg\Migration\Migration;

class OrdersTags extends Migration
{

    protected $repository = Repository::class . '.gnp';

    public function up()
    {
        $ordersUsersTags = $this->table('orders_tags');
        $ordersUsersTags->integer('order_id')->references('orders');
        $ordersUsersTags->varchar('type');
        $ordersUsersTags->varchar('value');

        $this->save();
    }

}