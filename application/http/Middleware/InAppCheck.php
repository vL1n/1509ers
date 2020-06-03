<?php

namespace app\http\Middleware;

use app\common\error\apiErrCode;
use app\common\jsonResponse\JsonResponse;

class InAppCheck
{
    use JsonResponse;
    public function handle($request, \Closure $next)
    {
        // 如果不存在user-agent就返回错误
        if(is_null($request->header('User-Agent'))){
            return $this->jsonInstanceData(apiErrCode::ERR_UNKNOWN[0],apiErrCode::ERR_UNKNOWN[1]);
        }

        $user_agent = $request->header('User-Agent');
        $is_mac = preg_match("/mac/i",$user_agent);
        $is_pc = preg_match("/windows/i",$user_agent);
        $is_android = preg_match("/android/i",$user_agent);
        $is_iphone = preg_match("/iphone/i",$user_agent);
        $is_mobile = preg_match("/mobile/i",$user_agent);
        $is_phone = preg_match("/phone/i",$user_agent);
        $is_ipad = preg_match("/ipad/i",$user_agent);
        $is_ipod = preg_match("/ipod/i",$user_agent);
        $is_wp = preg_match("/windows phone/i",$user_agent);
        $is_postman = preg_match("/postman/i",$user_agent);

        $is_mobile_android = preg_match("/X-Android/i",$user_agent);
        $is_mobile_ios = preg_match("/X-iOS/i",$user_agent);


        if($is_mobile_ios || $is_mobile_android){
            $request->InApp = 'mobile';
        }else{
            $request->InApp = 'web';
        }
        return $next($request);
    }
}
