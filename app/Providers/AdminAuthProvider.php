<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2019/5/7
 * Time: 12:16
 */

namespace App\Providers;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\EloquentUserProvider;

class AdminAuthProvider extends EloquentUserProvider
{

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $pain = $credentials['password'];
        $authPassword = $user->getAuthPassword();

        return password_verify($pain, $authPassword);
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {

    }


}