<?php
/**
 * Created by PhpStorm.
 * User: NickBai
 * Email: 876337011@qq.com
 * Date: 2019/2/28
 * Time: 8:24 PM
 */
namespace app\admin\controller;

use think\Controller;
use think\facade\Config;
use think\facade\Cookie;
use tool\Auth;

class Base extends Controller
{
    public function initialize()
    {
        $secret = Config::get('secret');
        $time = time();
        $sign = md5($time.$secret);
        Cookie::set('signA',['time'=>$time,'sign'=>$sign]);

        if(empty(session('admin_user_name'))){

            $this->redirect(url('login/index'));
        }

        $controller = lcfirst(request()->controller());
        $action = request()->action();
        $checkInput = $controller . '/' . $action;

        $authModel = Auth::instance();
        $skipMap = $authModel->getSkipAuthMap();

        if (!isset($skipMap[$checkInput])) {

            $flag = $authModel->authCheck($checkInput, session('admin_role_id'));

            if (!$flag) {
                if (request()->isAjax()) {
                    return json(reMsg(-403, '', '无操作权限'));
                } else {
                    $this->error('无操作权限');
                }
            }
        }

        $this->assign([
            'admin_name' => session('admin_user_name'),
            'admin_id' => session('admin_user_id')
        ]);
    }
}