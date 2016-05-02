<?php namespace Impero\Mysql\Migration;

use Pckg\Migration\Migration;

class CreateUserTable extends Migration
{

    public function up()
    {
        $siteTable = $this->table('database_users');

        $siteTable->varchar('name', 128)->required();
        $siteTable->integer('user_id')->references('users');

        $this->save();
    }

}