<?php


namespace app\http\Middleware;


use app\common\error\apiErrCode;
use app\common\jsonResponse\JsonResponse;
use think\facade\Cookie;
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

        // 检查是web还是mobile
        $InApp = $request->InApp;


        // web端sign鉴权
        if ($InApp == 'web'){
            // 初始化sign
            $sign = '';

            // 先确认是否存在sign
            if(!Cookie::has('signA')){
                return $this->jsonInstanceData(apiErrCode::ILLEGAL_REQUEST[0],apiErrCode::ILLEGAL_REQUEST[1]);
            }
            // 获取cookie里的sign 和 time
            $sign = Cookie::get('signA')['sign'];
            $param_time = Cookie::get('signA')['time'];

            // 检查时间存不存在
            if($param_time == ''){
                return $this->jsonInstanceData(apiErrCode::REQUEST_TIME_OUT[0],apiErrCode::REQUEST_TIME_OUT[1],'4');
            }

            // 参数拼接
            $sign_made_by_server = md5($param_time.$secretKey);
            // 鉴定sign
            if($sign != $sign_made_by_server){
                return $this->jsonInstanceData(apiErrCode::ILLEGAL_SIGN[0],apiErrCode::ILLEGAL_SIGN[1]);
            }

            $time_out = Config::get('Time_out');

            // 鉴定时间是否超时
            if($time - $param_time > '5'){
                return $this->jsonInstanceData(apiErrCode::REQUEST_TIME_OUT[0],apiErrCode::REQUEST_TIME_OUT[1],'3');
            }
        }


        // 移动端sign鉴权
        elseif ($InApp == 'mobile'){
            // 初始化sign
            $sign = '';

            // 先确认有无sign存在，无sign存在直接返回错误
            if($param['sign'] == ''){
                return $this->jsonInstanceData(apiErrCode::ILLEGAL_REQUEST[0],apiErrCode::ILLEGAL_REQUEST[1]);
            }
            $sign = $param['sign'];

            // 判断时间是否存在
            if($param['time'] == ''){
                return $this->jsonInstanceData(apiErrCode::REQUEST_TIME_OUT[0],apiErrCode::REQUEST_TIME_OUT[1],'2');
            }

            // 进行参数拼接
            unset($param['sign']);
            ksort($param);
            $req = http_build_query($param);
            $sign_made_by_serve = md5($req.$secretKey);
            if($sign != $sign_made_by_serve){
                return $this->jsonInstanceData(apiErrCode::ILLEGAL_SIGN[0],apiErrCode::ILLEGAL_SIGN[1]);
            }

            // 时间超过60秒（十三位时间戳）

            $time_out = Config::get('Time_out');

            if($time - $param['time'] > $time_out){
                return $this->jsonInstanceData(apiErrCode::REQUEST_TIME_OUT[0],apiErrCode::REQUEST_TIME_OUT[1],'1');
            }
        }
        else{
            return $this->jsonInstanceData(apiErrCode::ERR_UNKNOWN[0],apiErrCode::ERR_UNKNOWN[1],$InApp);
        }
        return $next($request);
    }
}