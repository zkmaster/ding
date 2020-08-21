<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use App\Jobs\LogJob;

class ExampleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Log::info(__METHOD__);
        return $next($request);
    }

    public function terminate($request, $response)
    {
        Log::info(__METHOD__);
        dispatch(new LogJob);
        Log::info($response->getStatusCode());
        Log::info($response->getContent());
    }
}
