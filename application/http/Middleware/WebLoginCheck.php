<?php


namespace app\http\Middleware;


use app\common\jsonResponse\JsonResponse;

class WebLoginCheck
{
    use JsonResponse;

    public function handle($request, \Closure $next){

        return $next($request);
    }
}