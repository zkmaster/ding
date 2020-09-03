<?php

# v1 版本路由
$route->version('v1', ['namespace' => 'App\Http\Controllers\V1'], function($route) {
    if(file_exists(__DIR__.'/v1/version.php') ) include_once(__DIR__.'/v1/version.php');
});

# 其它版本
# ...
