<?php


namespace app\http\Middleware;


use app\common\auth\jwt;
use app\common\error\apiErrCode;
use app\common\exception\apiException;
use app\common\jsonResponse\JsonResponse;
use think\cache\driver\Redis;
use think\Session;

class Token
{
    use JsonResponse;

    public function handle($request, \Closure $next){


        $inapp = $request->InApp;
        $redis = new Redis();
        $jwt = jwt::getInstance();

        // 请求出示手持令牌
        if($inapp == 'mobile'){
            $hand_token = $request->header('x-token');
        }elseif($inapp == 'web'){
            $hand_token = \think\facade\Session::get('web_token');
        }else{
            $hand_token = '';
        }

        // 查看手持令牌是否为空,为空的话就拒绝访问
        if($hand_token == ''){
            return $this->jsonInstanceData(apiErrCode::CHECK_FAILED[0],apiErrCode::CHECK_FAILED[1]);
        }

        // 开始检验手持令牌的有效性
        $jwt->setToken($hand_token);

        // token验证出错
        if( !$jwt->validate() || !$jwt->verify()){
            return $this->jsonInstanceData(apiErrCode::CHECK_FAILED[0],apiErrCode::CHECK_FAILED[1]);
        }

        // token验证成功
        // 获取uid,为以后的方法服务 可以通过request()->uid获取
        $request->uid = $jwt->getUid();
        $uid = $jwt->getUid();

        // 接下来验证手持令牌是否与redis缓存中的令牌一致
        // 获取redis储存的令牌
        if($inapp == 'mobile'){
            $redis_token = $redis->get($uid.'_'.'mobile_token');
        }
        elseif ($inapp == 'web'){
            $redis_token = $redis->get($uid.'_'.'web_token');
        }else{
            $redis_token = '';
        }

        // 当redis_token 不存在时
        if($redis_token == ''){
            return $this->jsonInstanceData(apiErrCode::CHECK_FAILED[0],apiErrCode::CHECK_FAILED[1]);
        }

        // 检查redis_token 和手持令牌的一致性,不一致时
        if($hand_token != $redis_token){
            return $this->jsonInstanceData(apiErrCode::CHECK_FAILED[0],apiErrCode::CHECK_FAILED[1]);
        }

        return $next($request);
    }
}