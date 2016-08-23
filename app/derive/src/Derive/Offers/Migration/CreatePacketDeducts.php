<?php namespace Derive\Offers\Migration;

use Pckg\Database\Repository;
use Pckg\Migration\Migration;

class CreatePacketDeducts extends Migration
{

    protected $repository = Repository::class . '.gnp';

    public function up()
    {
        /**
         * Create packets_deducts table.
         */
        $packetsDeducts = $this->table('packet_deducts');
        $packetsDeducts->integer('packet_id')->references('packets');
        $packetsDeducts->integer('addition_id')->references('additions');
        $packetsDeducts->decimal('value');

        $this->save();
    }

}