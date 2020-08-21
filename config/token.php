<?php

return [

    'secret' => env('TOKEN_SECRET', '7hDky*srz6gC'), // 加密盐

    'alg'    => 'HS256', // 加密方式

    'ttl'    => env('TOKEN_TTL', 30), // Token有效期 N minutes

    'refresh'=> env('TOKEN_REFRESH', false), // 是否刷新Token

    'refresh_ttl'=> env('TOKEN_REFRESH_TTL', 5), // 过期 N minutes 内 刷新Token

    'version'    => env('TOKEN_VER', '1.0.0'), // Token 版本

    // REDIS 地址
    'redis' => [
        'host' => env('TOKEN_REDIS_HOST'),
        'auth' => env('TOKEN_REDIS_PASSWORD'),
        'port' => env('TOKEN_REDIS_PORT')
    ],
];