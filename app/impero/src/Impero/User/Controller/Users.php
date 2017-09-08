<?php namespace Impero\User\Controller;

use Impero\User\Record\User;

class Users
{

    public function postUserAction()
    {
        $data = only(post()->all(), ['user_group_id', 'email', 'password', 'username', 'parent']);

        $user = User::create($data);

        return response()->respondWithSuccess([
                                                  'user' => $user,
                                              ]);
    }

    public function getUserAction()
    {
    }

}