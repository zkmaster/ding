<?php

$route->group(['prefix' => 'user', 'namespace' => 'User'], function ($route) {
    $route->get('info', [
        'uses' => 'UserController@getInfo',
        'as' => 'user.info.get',
        'type' => 1,
        'name' => '用户信息',
    ]);
});
