<?php namespace Impero\Mysql\Controller;

use Pckg\Framework\Controller;

class DatabaseApi extends Controller
{

    public function postDatabaseAction()
    {
        $data = post(['name']);

        $sql = 'CREATE DATABASE IF NOT EXISTS `' . $data['name'] . '` CHARACTER SET `utf8` COLLATE `utf8_general_ci`';
    }

}
