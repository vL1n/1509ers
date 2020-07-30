<?php


namespace app\http\Middleware;


use app\common\auth\jwt;
use app\common\error\apiErrCode;
use app\common\jsonResponse\JsonResponse;
use think\Session;

class WebLoginCheck
{
    use JsonResponse;

    public function handle($request, \Closure $next){

        return $next($request);
    }
}