<?php


namespace app\http\Middleware;



class WebAuth
{
    public function handle($request, \Closure $next){

        return $next($request);
    }
}