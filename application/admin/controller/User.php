<?php


namespace app\admin\controller;



use app\admin\validate\AdminValidate;
use app\admin\validate\UserValidate;
use app\common\model\Admin;
use tool\Log;

class User extends Base
{
    // 用户列表
    public function index()
    {
        if(request()->isAjax()) {

            $limit = input('param.limit');
            $userName = input('param.user_name');

            $where = [];
            if (!empty($userName)) {
                $where[] = ['username', 'like', $userName . '%'];
            }

            $user = new \app\common\model\User();
//            $list = $admin->getAdmins($limit, $where);
            $list = $user->getUsers($limit, $where);

            if(0 == $list['code']) {

                return json(['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $list['data']->all()]);
            }

            return json(['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []]);
        }

        return $this->fetch();
    }

    // 添加用户
    public function addUser()
    {
        if(request()->isPost()) {

            $param = input('post.');

            $validate = new UserValidate();
            if(!$validate->check($param)) {
                return ['code' => -1, 'data' => '', 'msg' => $validate->getError()];
            }

            $param['password'] = makePassword($param['password']);

            $user = new \app\common\model\User();
            $res = $user->addUser($param);

            Log::write("添加用户：" . $param['username']);

            return json($res);
        }

        $this->assign([
            'roles' => (new \app\common\model\Role())->getAllRoles()['data']
        ]);

        return $this->fetch('add');
    }

    // 编辑用户
    public function editUser()
    {
        if(request()->isPost()) {

            $param = input('post.');

            $validate = new UserValidate();
            if(!$validate->scene('edit')->check($param)) {
                return ['code' => -1, 'data' => '', 'msg' => $validate->getError()];
            }

            if(isset($param['password'])) {
                $param['password'] = makePassword($param['password']);
            }

            $user = new \app\common\model\User();
            $res = $user->editUser($param);

            Log::write("编辑用户：" . $param['username']);

            return json($res);
        }

        $userId = input('param.id');
        $user = new \app\common\model\User();

        $this->assign([
            'user' => $user->getUserById($userId)['data']
        ]);

        return $this->fetch('edit');
    }

    /**
     * 删除管理员
     * @return \think\response\Json
     */
    public function delUser()
    {
        if(request()->isAjax()) {

            $userId = input('param.id');

            $user = new \app\common\model\User();
            $res = $user->delUser($userId);

            Log::write("删除用户：" . $userId);

            return json($res);
        }
    }
}