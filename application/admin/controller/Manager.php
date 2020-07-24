<?php
/**
 * Created by PhpStorm.
 * User: NickBai
 * Email: 876337011@qq.com
 * Date: 2019/2/28
 * Time: 8:23 PM
 */
namespace app\admin\controller;

use app\common\model\Admin;
use app\admin\validate\AdminValidate;
use app\common\model\User;
use tool\Log;

class Manager extends Base
{
    // 管理员列表
    public function index()
    {
        if(request()->isAjax()) {

            $limit = input('param.limit');
            $adminName = input('param.admin_name');

            $where = [];
            if (!empty($adminName)) {
                $where[] = ['admin_name', 'like', $adminName . '%'];
            }

            $admin = new Admin();
            $list = $admin->getAdmins($limit, $where);

            if(0 == $list['code']) {

                return json(['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $list['data']->all()]);
            }

            return json(['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []]);
        }

        return $this->fetch();
    }

    // 添加管理员
    public function addAdmin()
    {
        if(request()->isPost()) {

            $param = input('post.');

            //验证输入的内容是否有误
            $validate = new AdminValidate();
            if(!$validate->check($param)) {
                return ['code' => -1, 'data' => '', 'msg' => $validate->getError()];
            }

            //判断是从现有用户中选择还是新建管理员用户
            // 从现有用户中选择
            if($param['exist_user'] != ''){
                $exist_user_id = $param['exist_user'];

                //从user表中获取该用户的信息
                $exist_user_info = User::where('id',$exist_user_id)->select();
                $param['admin_name'] = $exist_user_info[0]['username'];
                $param['admin_real_name'] = $exist_user_info[0]['real_name'];
                $param['admin_password'] = $exist_user_info[0]['password'];
                // 在user表中更新管理员数据
                User::where('id',$exist_user_id)->update(['is_admin'=>1]);

            }
            // 新建管理员用户
            elseif((!is_null($param['admin_name']))&&(!is_null($param['admin_password']))&&(!is_null($param['admin_real_name']))){
                //处理密码
                $param['admin_password'] = makePassword($param['admin_password']);
            }



//            准备输入数据库
            $admin = new Admin();
            $insertInfo = [
                'admin_name'=>$param['admin_name'],
                'admin_real_name'=>$param['admin_real_name'],
                'admin_password'=>$param['admin_password'],
                'role_id'=>$param['role_id'],
                'status'=>$param['status'],
                'add_time'=>date('Y-m-d H:i',time())
            ];
            $res = $admin->addAdmin($insertInfo);

            Log::write("添加管理员：" . $param['admin_name']);

            return json($res);
        }

        $user = User::where('id','<>','0')->select();

        $this->assign([
            'roles' => (new \app\common\model\Role())->getAllRoles()['data'],
            'users' => $user
        ]);

        return $this->fetch('add');
    }

    // 编辑管理员
    public function editAdmin()
    {
        if(request()->isPost()) {

            $param = input('post.');

            $validate = new AdminValidate();
            if(!$validate->scene('edit')->check($param)) {
                return ['code' => -1, 'data' => '', 'msg' => $validate->getError()];
            }

            if(empty($param['admin_password'])) {
                unset($param['admin_password']);
//                $param['admin_password'] = makePassword($param['admin_password']);
            }
            else{
                $param['admin_password'] = makePassword($param['admin_password']);
            }

            $admin = new admin();
            $res = $admin->editAdmin($param);

            Log::write("编辑管理员：" . $param['admin_name']);

            return json($res);
        }

        $adminId = input('param.admin_id');
        $admin = new admin();

        $this->assign([
            'admin' => $admin->getAdminById($adminId)['data'],
            'roles' => (new \app\common\model\Role())->getAllRoles()['data']
        ]);

        return $this->fetch('edit');
    }

    /**
     * 删除管理员
     * @return \think\response\Json
     */
    public function delAdmin()
    {
        if(request()->isAjax()) {

            $adminId = input('param.id');

            $admin = new admin();
            $res = $admin->delAdmin($adminId);

            Log::write("删除管理员：" . $adminId);

            return json($res);
        }
    }
}