<?php
/**
 * 与用户有关的操作全部放在这里
 *
 */
namespace app\api\controller\v1;


use app\common\auth\jwt;
use app\common\controller\File;
use app\common\error\apiErrCode;
use app\common\jsonResponse\JsonResponse;
use think\cache\driver\Redis;
use think\Controller;
use think\facade\Session;
use think\Request;

class User extends Controller
{
    use JsonResponse;
    protected $middleware = ['Token'];

    /**
     * 保存客户端上传的头像
     * @param Request $request
     * @return false|string
     * 使用base64编码传输数据
     */
    public function saveAvatar(Request $request){

        $uploads = $request->param();
        $img = base64_decode(str_replace(" ","+",$uploads['img']));
        $uid = $uploads['uid'];
        $format = $uploads['format'];

        // 判断文件格式
        if($format != 'png' && $format != 'jpg' && $format != 'jpeg'){
            return $this->jsonData(apiErrCode::ERR_FILE_TYPE[0],apiErrCode::ERR_FILE_TYPE[1]);
        }

        $path = './uploads/avatar/'.$uid.'.'.$format;
        $file = fopen($path,"w");
        fwrite($file,$img);
        fclose($file);

        //判断文件大小 大于10000kb就返回过大
        if(filesize($path) > 10000 * 1024){
            return $this->jsonData(apiErrCode::ERR_FILE_SIZE[0],apiErrCode::ERR_FILE_SIZE[1]);
        }

        // 在数据库写入路径
        $user = new \app\common\model\User();
        $user->updateUserAvatar($uid,$path);

        return $this->jsonData(apiErrCode::SUCCESS[0],apiErrCode::SUCCESS[1],[
            'path'=>$path
        ]);
    }

    /**
     * 客户端获取头像
     * @param Request $request
     * @return false|string
     */
    public function getAvatar(Request $request){

        $info = $request->param();
        $uid = $info['uid'];

        // 根据uid获得头像保存路径
        $userInfo = new \app\common\model\User();
        $userInfo_decode = json_decode($userInfo->getUserInfoById($uid),true);
        $avatar_path = $userInfo_decode['data']['avatar_path'];

        // 检查文件是否存在
        if(!file_exists($avatar_path)){
            return $this->jsonData(apiErrCode::FILE_NOEXIST[0],apiErrCode::FILE_NOEXIST[1]);
        }

        // 将头像文件编码成base64格式
        $arr_image = file_get_contents($avatar_path);
        $base64_image = base64_encode($arr_image);

        return $this->jsonData(apiErrCode::SUCCESS[0],apiErrCode::SUCCESS[1],$base64_image);
    }

    /**
     * 更新token
     * @param Request $request
     * @return string
     */
    public function updateToken(Request $request){

        // 从中间件解析token的同时获取到uid
        $uid = $request->uid;

        // 开始制作token
        $jwt = jwt::getInstance();
        $token = $jwt->setUid($uid)->encode()->getTOken();

        // 将新的token写入redis
        $redis =new Redis();
        //判断请求app
        $inapp = $request->InApp;
        if($inapp == 'mobile'){
            $redis->set($uid.'_'.'mobile_token',$token);
        }
        elseif ($inapp == 'web'){
            $redis->set($uid.'_'.'web_token',$token);
            Session::set('web-token',$token);
        }

        // 返回新token
        return $this->jsonSuccess($token);
    }

    /**
     * 用户登出
     * @param Request $request
     * @return string
     */
    public function userLogout(Request $request){

        $redis = new Redis();
        //先判断请求app
        $inapp = $request->InApp;
        if ($inapp == 'web'){
            Session::delete('web-token');
        }
        return $this->jsonSuccess();
    }
}