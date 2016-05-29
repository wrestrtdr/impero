<?php namespace Impero\Ftp\Migration;

use Pckg\Migration\Migration;

class CreateFtpTable extends Migration
{

    public function up()
    {
        $ftpTable = $this->table('ftps');

        $ftpTable->varchar('username', 128)->required();
        $ftpTable->varchar('password', 255)->required();
        $ftpTable->varchar('path', 255)->required();
        $ftpTable->integer('user_id')->references('users');

        $this->save();
    }

}