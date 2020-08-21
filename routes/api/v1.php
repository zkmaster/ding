<?php

$router->group(['namespace' => 'v1', 'prefix' => 'api'], function () use ($router) {

    $router->post('v1/user/login', [
        'uses' => 'UserController@loginIn'
    ]);



    $router->group(['middleware' => ['token']], function () use ($router) {

        $router->get('v1/home/index', [
            'uses' => 'HomeController@index'
        ]);

        $router->get('v1/user/info', [
            'uses' => 'UserController@userInfo',
            'access' => 0,
        ]);
    });

});