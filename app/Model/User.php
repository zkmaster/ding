<?php

namespace App\Model;

class User extends BaseModel
{
    protected $table = 'user';

    protected $hidden = [
        'password',
        'salt',
        'token'
    ];

    // 用户状态
    const HIDDEN = 'hidden'; // 禁用
    const NORMAL = 'normal'; // 启用
}
