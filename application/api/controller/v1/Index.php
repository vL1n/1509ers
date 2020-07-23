<?php
/**
 * 用来存放首页的api
 *
 */

namespace app\api\controller\v1;


use app\common\error\apiErrCode;
use app\common\jsonResponse\JsonResponse;
use think\Controller;
use think\Request;

class Index extends Controller
{
    use JsonResponse;
    protected $middleware = ['Token'];

    public function getInitialData(Request $request){

        // 从中间件中得到token的uid
        $uid = $request->uid;

        //由uid获取当前用户的信息
        $user = new \app\common\model\User();
        $userInfo = $user->getUserInfoById($uid);

        $userInfo_decode = json_decode($userInfo,true);
        $userInfo_decode['data']['password'] = '';
        $data = [
            'userInfo'=>$userInfo_decode['data'],
        ];
        return $this->jsonSuccess($data);
    }


}