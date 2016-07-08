<?php namespace Derive\Platform\Migration;

use Pckg\Migration\Migration;

class CreatePlatformTables extends Migration
{

    public function up()
    {
        $platforms = $this->table('platforms');
        $platforms->varchar('title');
        $platforms->text('database');

        $platformsUsers = $this->table('platforms_users');
        $platformsUsers->integer('user_id')->references('users');
        $platformsUsers->integer('platform_id')->references('platforms');

        $this->save();
    }

}