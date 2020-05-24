<?php


namespace app\http\Middleware;


use app\common\error\apiErrCode;
use app\common\jsonResponse\JsonResponse;
use think\facade\Config;

class SignCheck
{
    use JsonResponse;
    public function handle($request, \Closure $next)
    {
        // 将request解析出来
        $param = $request->param();

        // 初始化key
        $secretKey = Config::get('secret');

        // 初始化时间
        $time = time();

        // 初始化sign
        $sign = '';

        // 先确认有无sign存在，无sign存在直接返回错误
        if($param['sign'] == ''){
            return $this->jsonInstanceData(apiErrCode::ILLEGAL_REQUEST[0],apiErrCode::ILLEGAL_REQUEST[1]);
        }
        $sign = $param['sign'];

        // 判断时间是否存在及超时
        if($param['time'] == ''){
            return $this->jsonInstanceData(apiErrCode::REQUEST_TIME_OUT[0],apiErrCode::REQUEST_TIME_OUT[1]);
        }

        // 进行参数拼接
        unset($param['sign']);
        ksort($param);
        $req = http_build_query($param);
        $sign_made_by_serve = md5($req.$secretKey);
        if($sign != $sign_made_by_serve){
            return $this->jsonInstanceData(apiErrCode::ILLEGAL_SIGN[0],apiErrCode::ILLEGAL_SIGN[1]);
        }

        // 时间超过十秒（十三位时间戳）

        $time_out = Config::get('Time_out');

        if($time - $param['time'] > $time_out){
            return $this->jsonInstanceData(apiErrCode::REQUEST_TIME_OUT[0],apiErrCode::REQUEST_TIME_OUT[1]);
        }
        return $next($request);
    }
}