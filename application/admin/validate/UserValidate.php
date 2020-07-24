<?php


namespace app\admin\validate;


use think\Validate;

class UserValidate extends  Validate
{
    protected $rule =   [
        'username'  => 'require',
//        'password'   => 'require',
//        'role_id' => 'require',
//        'status' => 'require'
    ];

    protected $message  =   [
        'username.require' => '管理员名称不能为空',
//        'password.require'   => '管理员密码不能为空',
//        'role_id.require'   => '所属角色不能为空',
//        'status.require'   => '状态不能为空'
    ];

    protected $scene = [
        'edit'  =>  ['username', 'phone', 'email', 'real_name', 'school_id', 'avatar_path', 'status', 'password', 'college', 'sex', 'phone_checked', 'pwd_changed', 'is_admin']
    ];
}