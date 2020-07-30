<?php


namespace app\test\controller;


use app\common\auth\jwt;
use app\common\error\apiErrCode;
use think\Session;

class User extends Base
{
    /**
     * @return false|string
     */
    public function getUserName(){
        $token = (new Session())->get('web_token');
        $jwt = jwt::getInstance();


        // 没有token
        if($token == ''){
            return $this->jsonApiError(apiErrCode::NO_TOKEN);
        }

        // token验证出错
        if( !$jwt->validate() || !$jwt->verify()){
            return $this->jsonApiError(apiErrCode::CHECK_FAILED);
        }

        $user = new \app\common\model\User();
        $user_info = $user->getUserInfoById($jwt->getUid());
        $user_info_decode = json_decode($user_info,true);
        $username = $user_info_decode['username'];
        return $this->jsonSuccess([
            'username'=>$username
        ]);
    }

    public function login(){
        return $this->fetch();
    }

    public function profile(){
        return $this->fetch();
    }
}