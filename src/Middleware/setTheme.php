<?php namespace igaster\laravelTheme\Middleware;

namespace App\Http\Middleware;

use Closure;

class setTheme
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $themeName)
    {
        \Theme::set($themeName);
        return $next($request);
    }
}
