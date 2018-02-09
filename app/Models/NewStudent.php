<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class NewStudent extends Authenticatable implements JWTSubject
{
    use HasRoles;

    protected $guard_name = 'api';
    protected $fillable = [
        'name', 'reg_num', 'password', 'weixin_openid', 'weixin_unionid', 'is_fill', 'is_confirm', 'is_lottery', 'is_admit', 'admit_remark', 'class_remark'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['auth_type' => 'new_student'];
    }
}
