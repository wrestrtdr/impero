<?php namespace Impero\Services\Service;

class PhpFpm extends Php
{

    protected $service = 'php7.0-fpm';

    protected $name = 'PHP-FPM';

    public function getVersion()
    {
        return null;
    }

}