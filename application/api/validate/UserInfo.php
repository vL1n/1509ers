<?php


namespace app\api\validate;


use think\Validate;

class UserInfo extends Validate
{
    protected $rule =   [
        'username'  => 'require|max:20',
        'school_id'   => 'number|between:1,69',
        'email' => 'email',
    ];

    protected $message  =   [
        'username.require'   => '用户名为必填项',
        'username.max'       => '名称最多不能超过20个字符',
        'school_id.number'   => '学号必须是数字',
        'school_id.between'  => '学号只能在1-69之间',
        'email'              => '邮箱格式错误',
    ];
}