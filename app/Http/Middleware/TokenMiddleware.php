<?php

namespace App\Http\Middleware;

use Closure;
use App\Helper\Token;

class TokenMiddleware
{
    /**
     * Token 身份验证
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $token);
        $res = Token::decode($token);
        if (!$res) return show_error(401, '请登录！');
        return $next($request);
    }
}
