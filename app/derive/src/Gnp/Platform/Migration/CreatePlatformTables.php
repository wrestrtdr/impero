<?php namespace Gnp\Platform\Migration;

use Pckg\Migration\Migration;

class CreatePlatformTables extends Migration
{

    public function up() {
        $platforms = $this->table('platforms');
        $platforms->varchar('title');
        $platforms->text('database');

        $this->save();
    }

}