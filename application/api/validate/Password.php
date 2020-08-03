<?php


namespace app\api\validate;


use think\Validate;

class Password extends Validate
{
    protected $rule = [
        'password'  => 'require|min:8|alphaDash|confirm'
    ];

    protected $message  =   [
        'password.require'      => '密码为必填项',
        'password.min'          => '密码不能少于8个字符',
        'password.alphaDash'    => '密码只能由字母和数字，下划线_及破折号-组成',
        'password.confirm'      => '两次输入的密码不一致'
    ];
}