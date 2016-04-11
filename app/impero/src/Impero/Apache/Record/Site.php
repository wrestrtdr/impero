<?php namespace Impero\Apache\Record;

use Impero\Apache\Entity\Sites;
use Pckg\Database\Record;

class Site extends Record
{

    protected $entity = Sites::class;

    public function getUserPath()
    {
        return '/www/' . $this->user->username . '/';
    }

    public function getDomainPath()
    {
        return $this->getUserPath() . $this->server_name . '/';
    }

    public function getLogPath()
    {
        return $this->getDomainPath() . 'logs/';
    }

    public function getHtdocsPath()
    {
        return $this->getDomainPath() . 'htdocs/';
    }

    public function getSslPath()
    {
        return $this->getDomainPath() . 'ssl/';
    }

    public function getVirtualhost()
    {
        return $this->getInsecureVirtualhost() . $this->getSecureVirtualhost();
    }

    public function getBasicDirectives()
    {
        $directives = [
            'ServerName ' . $this->name,
            'DocumentRoot ' . $this->document_root,
        ];

        if ($this->server_alias) {
            $directives[] = 'ServerAlias ' . $this->server_alias;
        }

        if ($this->error_log) {
            $directives[] = 'ErrorLog ' . $this->getLogPath() . 'error.log';
        }

        if ($this->access_log) {
            $directives[] = 'CustomLog ' . $this->getLogPath() . 'access.log combined';
        }

        return $directives;
    }

    public function getInsecureVirtualhost()
    {
        $directives = $this->getBasicDirectives();

        return '<VirtualHost *:80>
    ' . implode("\t", $directives) . '
</VirtualHost>';
    }

    public function getSecureVirtualhost()
    {
        if (!$this->ssl) {
            return;
        }

        $directives = $this->getBasicDirectives();
        $directives[] = 'SSLEngine on';
        $directives[] = 'SSLCertificateFile ' . $this->getSslPath() . $this->ssl_certificate_file;
        $directives[] = 'SSLCertificateKeyFile ' . $this->getSslPath() . $this->ssl_certificate_key_file;

        return '<VirtualHost *:443>
    ' . implode("\t", $directives) . '
</VirtualHost>';
    }

}
