<?php

namespace App\Http\Middleware;

use Closure;

class XssMiddleware
{
    /**
     * 预防 Xss 攻击
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!in_array(strtolower($request->method()), ['put', 'post'])) {
            return $next($request);
        }

        $input = $request->all();
        array_walk_recursive($input, function (&$input) {
            $input = strip_tags($input);
        });
        $request->merge($input);
//        dd($request->all());

        return $next($request);
    }
}
