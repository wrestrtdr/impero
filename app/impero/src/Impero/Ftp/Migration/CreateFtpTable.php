<?php namespace Impero\Ftp\Migration;

use Pckg\Migration\Migration;

class CreateFtpTable extends Migration
{

    public function up()
    {
        $siteTable = $this->table('ftps');

        $siteTable->varchar('username', 128)->required();
        $siteTable->varchar('password', 255)->required();
        $siteTable->varchar('path', 255)->required();
        $siteTable->integer('user_id')->references('users');

        $this->save();
    }

}