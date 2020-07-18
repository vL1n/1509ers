<?php


namespace app\api\controller\v1;


use app\api\validate\UserInfo;
use app\common\auth\jwt;
use app\common\error\apiErrCode;
use app\common\jsonResponse\JsonResponse;
use app\common\jsonResponse\RegCheck;
use think\Cache;
use think\cache\driver\Redis;
use think\Controller;
use think\facade\Session;
use think\Request;

class Init extends Controller
{
    use JsonResponse;

    /**
     * @param Request $request
     * @return false|string
     * @throws \think\Exception
     */
    public function userLogin(Request $request){

        // 获取请求参数
        $info = $request->param();
        $user = new \app\common\model\User();

        // 初始化请求方式
        $type = $info['loginType'];
        if($type != 'phone' && $type != 'email'){
            return $this->jsonApiError(apiErrCode::ERR_LOGIN_TYPE);
        }

        // 从模型中查询信息并解码
        $userInfo = $user->getUserInfoByField($type,$info['param']);
        $userInfo_decode = json_decode($userInfo,true);

        // 模型返回异常
        if($userInfo_decode->code != 0){
            return $this->jsonData($userInfo_decode->code,$userInfo_decode->msg);
        }

        // 如果还没验证手机号或者更改密码，那么返回
        if (($userInfo_decode->data)->phone_checked == '0' || ($userInfo_decode->data)->pwd_changed == '0'){
            return $this->jsonApiError(apiErrCode::PHONE_CHECK_FAILED);
        }


        // 密码错误
        if(md5($info['password']) != ($userInfo_decode->data)->password){
            return $this->jsonApiError(apiErrCode::ERR_PASSWORD);
        }


        /**
         * 验证成功之后的操作
         */
        //登陆通过，写入数据库，维护最近登陆时间
        $uid = ($userInfo_decode->data)->id;
        $user->where('id',$uid)->setInc('login_count');
        $user->updateUserInfoById($uid,['last_login'=>time()]);

        // 开始制作token
        $jwt = jwt::getInstance();
        $token = $jwt->setUid($uid)->encode()->getToken();

        // 把token写入redis
        $redis = new Redis();
        // 判断登陆app
        $inapp = $request->InApp;
        if($inapp == 'mobile'){
            $redis->set($uid.'_'.'mobile_token',$token);
        }
        elseif ($inapp == 'web'){
            $redis->set($uid.'_'.'web_token',$token);
            Session::set('web_token',$token);
        }

        return $this->jsonSuccess([
            'token' => $token
        ]);
    }

    /**
     * 注册api，目前已经不需要注册
     * @param Request $request
     * @return false|string
     */
    public function userRegister(Request $request){

        $info = $request->param();
        $user = new \app\common\model\User();
        $data = [
            'username'=>$info['username'],
            'email' => $info['email'],
            'school_id' => $info['school_id'],
            'password' => md5($info['password']),
            'avatar_path'=> './uploads/avatar/default.jpg',
            'login_count'=>0
        ];

        // 验证传输数据
        $validate = new UserInfo();
        if (!$validate->check($data)) {
            return $this->jsonApiError(apiErrCode::ERR_SYSTEM,$validate->getError());
        }

        // 查询学号存在情况
        $res = $user->searchUserInfoByField('school_id',$info['school_id']);
            // 学号已存在
        if(json_decode($res,true)['code'] != 0){
            return $this->jsonApiError(apiErrCode::ERR_COMMON,json_decode($res,true)['msg']);
        }

        // 写入数据库,新建记录
        $insert = $user->insert($data);

        // 返回成功信息
        return $this->jsonSuccess();

    }

    public function phoneCheck(Request $request){

        $info = $request->param();


    }

    public function userLogin1(Request $request){

        // 处理请求数据
        $info = $request->param();
        $user = new \app\common\model\User();

        // 判断登陆方式
        $account = $info['account'];


    }

}