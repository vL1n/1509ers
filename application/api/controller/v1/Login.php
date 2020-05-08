<?php


namespace app\api\controller\v1;


use app\common\auth\jwt;
use app\common\error\apiErrCode;
use app\common\jsonResponse\JsonResponse;
use think\Controller;
use think\Request;

class Login extends Controller
{
    use JsonResponse;

    /**
     * @param Request $request
     * @return false|string
     * @throws \think\Exception
     */
    public function userLogin(Request $request){

        $info = $request->param();
        $user = new \app\common\model\User();
        $userInfo = $user->getUserInfoByUserName($info['username']);
        $userInfo_decode = json_decode($userInfo);
        // 模型返回异常
        if($userInfo_decode->code != 0){
            return $this->jsonData($userInfo_decode->code,$userInfo_decode->msg);
        }
        // 密码错误
        if(md5($info['password']) != ($userInfo_decode->data)->password){
            return $this->jsonData(apiErrCode::ERR_PASSWORD[0],apiErrCode::ERR_PASSWORD[1]);
        }

        //登陆通过，写入数据库，维护最近登陆时间
        $uid = ($userInfo_decode->data)->id;
        $user->where('id',$uid)->setInc('login_count');
        $user->updateUserInfoById($uid,['last_login'=>time()]);

        // 开始制作token
        $jwt = jwt::getInstance();
        $token = $jwt->setUid($uid)->encode()->getToken();

        return $this->jsonData(apiErrCode::SUCCESS[0],apiErrCode::SUCCESS[1],[
            'token' => $token
        ]);
    }
}