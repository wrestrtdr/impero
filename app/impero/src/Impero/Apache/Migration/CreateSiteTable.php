<?php namespace Impero\Apache\Migration;

use Pckg\Migration\Migration;

class CreateSiteTable extends Migration
{

    public function up()
    {
        $siteTable = $this->table('site');
        $siteTable->id();
        $siteTable->varchar('server_name', 128)->required();
        $siteTable->varchar('server_alias', 255)->nullable();
        $siteTable->varchar('document_root', 255)->required();
        $siteTable->varchar('ssl', 16)->nullable();
        $siteTable->varchar('ssl_certificate_key', 128)->nullable();
        $siteTable->varchar('ssl_certificate_key_file', 128)->nullable();
        $siteTable->boolean('ssl_letsencrypt_autorenew', 128);
        $siteTable->boolean('error_log')->default(1);
        $siteTable->boolean('access_log')->default(1);
    }

}