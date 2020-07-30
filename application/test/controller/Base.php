<?php


namespace app\test\controller;


use app\common\jsonResponse\JsonResponse;
use think\Controller;
use think\facade\Config;
use think\facade\Cookie;

class Base extends Controller
{
    use JsonResponse;
    // 初始化函数，把密钥啥的写入cookie
    public function initialize()
    {
        $secret = Config::get('secret');
        $time = time();
        $sign = md5($time.$secret);
        Cookie::set('signA',['time'=>$time,'sign'=>$sign]);
    }
}