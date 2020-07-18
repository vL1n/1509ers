<?php


namespace app\common\regCheck;


trait regCheck
{
    /**
     * 验证输入的邮件地址是否合法
     * @access  public
     * @param   string      $email      需要验证的邮件地址
     * @return bool
     */
    function is_email($email)
    {
        $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
        if (strpos($email, '@') !== false && strpos($email, '.') !== false)
        {
            if (preg_match($chars, $email))
            {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     * 验证输入的手机号码是否合法
     * @access public
     * @param string $mobile_phone
     * 需要验证的手机号码
     * @return bool
     */
    function is_mobile_phone ($mobile_phone)
    {
        $chars = "/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$/";
        if(preg_match($chars, $mobile_phone))
        {
            return true;
        }
        return false;
    }
}