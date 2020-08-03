<?php


namespace app\api\controller\v1;


use app\api\validate\Password;
use app\common\auth\jwt;
use app\common\error\apiErrCode;
use app\common\jsonResponse\JsonResponse;
use app\common\model\LoginLog;
use app\common\model\Sms;
use app\common\model\User;
use app\common\regCheck\RegCheck;
use app\common\sms\AliSmsUtil;
use think\cache\driver\Redis;
use think\Controller;
use think\facade\Session;
use think\Request;

class Utils extends Controller
{
    use JsonResponse;
    use RegCheck;

    /**
     * @param Request $request
     * @return false|string
     * @throws \think\Exception
     */
    public function userLogin(Request $request){

        // 处理请求数据
        $info = $request->param();
        $user = new User();
        $log = new LoginLog();

        // 查看是否提交空表单
        if($info['account'] == '' || $info['password'] == ''){
            return $this->jsonApiError(apiErrCode::ERR_BLANK);
        }

        // 判断登陆方式(学号|邮箱|手机号)
        $account = $info['account'];
        $loginType = $this->check_type($account);

        // 非手机号/邮箱/学号登陆
        if($loginType == ''){
            return $this->jsonApiError(apiErrCode::ERR_LOGIN_TYPE);
        }

        // 带数据进数据库查询
        $userInfo = $user->getUserInfoByField($loginType,$account);
        $userInfo_decode = json_decode($userInfo,true);

        // 模型返回异常
        if($userInfo_decode['code'] != 0){
            return $this->jsonData($userInfo_decode['code'],$userInfo_decode['msg']);
        }

        // 密码错误
        if(md5($info['password']) != $userInfo_decode['data']['password']){
            $log->writeLoginLog($userInfo_decode['data']['real_name'],2);
            return $this->jsonApiError(apiErrCode::ERR_PASSWORD);
        }

        // 手机号未绑定或者账号密码未修改
        if($userInfo_decode['data']['phone_checked'] == '0' || $userInfo_decode['data']['pwd_changed'] == '0'){
            $data = ['phone_checked'=>1,'pwd_changed'=>1,'uid'=>$userInfo_decode['data']['id']];
            // 账号密码正确,检查是否未绑定手机号
            if ($userInfo_decode['data']['phone_checked'] == '0'){
                $data['phone_checked']=0;
            }
            //检查初始密码是否已经更改
            if ($userInfo_decode['data']['pwd_changed'] == '0'){
                $data['pwd_changed']=0;
            }
            return $this->jsonApiError(apiErrCode::PWD_PHONE_NEED_CHECK,$data);
        }

        /**
         * 验证成功之后的操作
         */
        //登陆通过，写入数据库，维护最近登陆时间,写入ip
        $uid = $userInfo_decode['data']['id'];
        $user->where('id',$uid)->setInc('login_count');
        $user->updateUserInfoById($uid,['last_login'=>time()]);
        $user->where('id',$uid)->update(['last_ip'=>$request->ip()]);

        // 开始制作token
        $jwt = jwt::getInstance();
        $token = $jwt->setUid($uid)->encode()->getToken();

        // 把token写入redis
        $redis = new Redis();

        // 判断登陆app
        $inapp = $request->InApp;
        if($inapp == 'mobile'){
            $redis->setex($uid.'_'.'mobile_token',604800,$token);
        }
        elseif ($inapp == 'web'){
            $redis->setex($uid.'_'.'web_token',604800,$token);
            Session::set('web_token',$token);
        }

        // 写入login_log
        $log->writeLoginLog($userInfo_decode['data']['real_name'],1);

        return $this->jsonSuccess([
            'token' => $token
        ]);

    }

    /**
     * @param Request $request
     * @return false|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sendSMS(Request $request){

        $info = $request->param();
        $phone = $info['phone'];
        $uid = $info['id'];
        $ip = $request->ip();

        // 初始化模型
        $sms = new Sms();

        // 初始化验证器
        $validate = new \app\api\validate\SMS();

        $need_validate = [
            'phone'=>$phone,
            'id'=>$uid
        ];
        if (!$validate->check($need_validate)) {
            return $this->jsonData(apiErrCode::ERR_SYSTEM[0],$validate->getError());
        }

        // 获取今天内该请求ip申请的短信验证码次数
        $res = $sms->where('request_ip',$ip)->whereTime('time','today')->count();
        // 获取该ip最近的一次短信验证码数据
        $res2 = $sms->where('request_ip',$ip)->whereTime('time','today')->order('time desc')->find();


        if($res || $res2){
            // 如果距离上一次请求不足3分钟，则拒绝访问
            if(time() - strtotime($res2['time']) < 180){
                return $this->jsonApiError(apiErrCode::REQUEST_FREQUENTLY,[strtotime($res2['time']),time()]);
            }
            // 如果同一个ip同一天内请求次数超过了5次,则拒绝访问
            if ($res >= 5) {
                return $this->jsonApiError(apiErrCode::SMS_5_ALLOWED_A_DAY);
            }
        }

        // 随机生成六位数验证码
        $code = mt_rand(100000,999999);
        $sender = new AliSmsUtil();

        // 发送验证码
        $sender::sendSmsCode($phone,$code);
        // 写入数据库
        $data = [
            'id'            =>NULL,
            'user_id'       =>$uid,
            'phone_number'  =>$phone,
            'code'          =>$code,
            'request_ip'    =>$ip,
            'time'          =>date('Y-m-d H:i:s', time()),
            'used'          =>0
        ];
        $sms->insert($data);

        return $this->jsonSuccess();
    }

    /**
     * @param Request $request
     * @return false|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function validateSMS(Request $request){

        $info = $request->param();
        $phone = $info['phone'];
        $ip = $request->ip();
        $code = $info['code'];

        $data_need_validate = [
            'phone'=>$phone,
            'code'=>$code
        ];

        // 初始化验证器,验证传来的数据是否正确
        $validate = new \app\api\validate\SMS();
        if (!$validate->check($data_need_validate)) {
            return $this->jsonData(apiErrCode::ERR_SYSTEM[0],$validate->getError());
        }

        // 初始化模型
        $sms = new Sms();
        $user = new User();

        // 根据手机号从数据库中获取该手机最新的一条验证码信息,find()函数只返回一条信息，time desc 为根据时间倒序寻找，也就是最新一条
        $sms_info = $sms->where('phone_number',$phone)->order('time desc')->find();

        if(!$sms_info){
            return $this->jsonApiError(apiErrCode::SMS_OR_PHONE_ERROR);
        }
        // 1.判断验证码是否正确
        if ($sms_info['code'] != $code){
            return $this->jsonApiError(apiErrCode::SMS_OR_PHONE_ERROR);
        }
        // 2.判断验证码是否在有效期内(5分钟)
        elseif (strtotime($sms_info['time']) - time() > 300){
            return $this->jsonApiError(apiErrCode::SMS_TIME_OUT);
        }
        // 3.判断验证码是否已经被使用
        elseif ($sms_info['used'] != '0'){
            return $this->jsonApiError(apiErrCode::SMS_USED);
        }
        // 以上三判断通过，写入数据库更新该条验证码信息,返回成功
        $sms->where('id',$sms_info['id'])->update(['used'=>1]);

        return $this->jsonSuccess();
    }

    /**
     * @param Request $request
     * @return false|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function bindPhoneToAccount(Request $request){

        $info = $request->param();
        $uid = $info['id'];
        $phone = $info['phone'];

        // 初始化验证器
        $validate = new \app\api\validate\SMS();

        $data_need_validate = [
            'id'=>$uid,
            'phone'=>$phone
        ];

        // 验证传来的数据
        if(!$validate->check($data_need_validate)){
            return $this->jsonData(apiErrCode::ERR_SYSTEM[0],$validate->getError());
        }

        // 初始化模型
        $user = new User();
        $user->where('id',$uid)->update(['phone'=>$phone,'phone_checked'=>'1']);

        return $this->jsonSuccess();
    }

    public function pwdChange(Request $request){
        $info = $request->param();
        $new_password = $info['new_password'];
        $new_password_confirm = $info['new_password_confirm'];
        $uid = $info['id'];

        // 需要验证的数据
        $data_need_validate = [
            'password'=>$new_password,
            'password_confirm'=>$new_password_confirm
        ];

        // 验证
        $validate = new Password();
        if(!$validate->check($data_need_validate)){
            return $this->jsonData(apiErrCode::ERR_SYSTEM[0],$validate->getError());
        }

        // 验证通过，写入数据库
        $user = new User();
        $user->where('id',$uid)->update(['password'=>md5($new_password),'pwd_changed'=>1]);

        return $this->jsonSuccess();
    }
}