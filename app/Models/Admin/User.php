<?php

namespace App\Models\Admin;

use App\Models\IFormFields;
use App\Models\Traits\Admin\UserTrait;
use Illuminate\Foundation\Auth\User as AuthenticatableUser;

class User extends AuthenticatableUser implements IFormFields
{

    use UserTrait;

    protected $table = 'admin';
    protected $primaryKey = 'id';
    protected $fillable = [
        'login_id',
        'username',
        'pwd',
        'group',
    ];

    public function createPwd($password) {
        $this['pwd'] = password_hash($password, PASSWORD_DEFAULT);
    }

    public function verify($password) {
        return $this->exists && password_verify($password, $this->pwd);
    }

    public function getAuthPassword()
    {
        if ($this->exists) {
            return $this->pwd;
        }

        return '';
    }


}
