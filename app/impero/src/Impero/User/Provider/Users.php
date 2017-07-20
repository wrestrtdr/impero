<?php namespace Impero\User\Provider;

use Impero\User\Controller\Users as UsersController;
use Pckg\Auth\Provider\ApiAuth;
use Pckg\Framework\Provider;

class Users extends Provider
{

    public function routes()
    {
        return [
            routeGroup([
                           'urlPrefix'  => '/api/',
                           'namePrefix' => 'api',
                           'controller' => UsersController::class,
                       ], [
                           '.user' => route('user', 'user'),
                       ]),
        ];
    }

    public function providers()
    {
        return [
            ApiAuth::class,
        ];
    }

}