<?php namespace Impero\Apache\Migration;

use Pckg\Migration\Migration;

class CreateFtpTable extends Migration
{

    public function up()
    {
        $siteTable = $this->table('site');
        $siteTable->id();
        $siteTable->varchar('username', 128)->required();
        $siteTable->varchar('password', 255)->required();
        $siteTable->varchar('path', 255)->required();
    }

}