<?php


namespace App\Model;


class DataUser extends BaseModel
{
    protected $table = 'user';

    protected $hidden = [
        'password'
    ];

    protected $fillable = ['username', 'mobile', 'status', 'tid'];
}