<?php


namespace app\api\validate;


use think\Validate;

class SMS extends Validate
{
    protected $rule = [
        'phone'=>'require|mobile',
        'code'=>'number|between:100000,999999',
        'id' =>'number|between:0,99'
    ];

    protected  $message = [
        'phone.require' => '手机号码不能为空',
        'phone.mobile' => '手机号码格式不对',
        'code.number'=>'验证码只能是数字',
        'code.between'=>'验证码应为六位整数',
        'id.number'=>'id必须为数字',
        'id.between'=>'id应为两位数'
    ];
}