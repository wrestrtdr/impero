<?php namespace Impero\Apache\Migration;

use Pckg\Migration\Migration;

class CreateSiteTable extends Migration
{

    public function up()
    {
        $siteTable = $this->table('sites');

        $siteTable->id();
        $siteTable->integer('user_id')->references('users', 'id');

        $siteTable->varchar('server_name', 128)->required();
        $siteTable->varchar('server_alias', 255)->nullable();
        $siteTable->varchar('document_root', 255)->required();

        $siteTable->varchar('ssl', 16)->nullable();
        $siteTable->varchar('ssl_certificate_file', 128)->nullable();
        $siteTable->varchar('ssl_certificate_key_file', 128)->nullable();
        $siteTable->varchar('ssl_certificate_chain_file', 128)->nullable();
        $siteTable->datetime('ssl_letsencrypt_autorenew')->nullable();

        $siteTable->boolean('error_log')->setDefault(1);
        $siteTable->boolean('access_log')->setDefault(1);

        $siteTable->index('ssl');
        $siteTable->unique('server_name');

        $siteTable->timeable();

        $this->save();
    }

}