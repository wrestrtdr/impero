<?php namespace Impero\Mysql\Controller;

use Pckg\Framework\Controller;

class DatabaseUserApi extends Controller
{

    public function postUserAction()
    {
        $data = post(['username', 'password']);

        $sql = 'CREATE USER IF NOT EXISTS `' . $data['name'] . '`@`localhost` IDENTIFIED BY \'' . $data['password'] .
               '\'';
    }

    public function postPermissionsAction()
    {
        $data = [
            'database' => 'pckg_derive',
            'user'     => 'hi_shop',
        ];
        $permissions = [
            'client' => 'SELECT, UPDATE, DELETE, INSERT',
        ];

        $sql = 'GRANT ' . $permissions['client'] . ' ON `' . $data['database'] . '`.* TO `' . $data['user'] .
               '`@`localhost`';
    }

}
