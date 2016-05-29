<?php namespace Impero\Apache\Migration;

use Pckg\Migration\Migration;

class AlterUserTable extends Migration
{

    public function up()
    {
        $users = $this->table('users');

        $users->varchar('username');

        $this->save();
    }

}