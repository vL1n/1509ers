<?php


namespace app\common\error;


class apiErrCode
{

    const SUCCESS           =   [0, 'success'];
    const ERR_UNKNOWN       =   [1, '未知错误'];
    const ERR_SYSTEM        =   [2, '已知系统错误'];
    const ERR_COMMON        =   [3, '通用错误'];

    /**
     * 登陆异常
     */
    const ERR_SUBMIT        =   [1001, '提交异常'];
    const USR_NOEXIST       =   [1002, '用户名不存在'];
    const ERR_PASSWORD      =   [1003, '密码错误'];
    const ERR_BLANK         =   [1004, '有项目未填写'];
    const USR_FORBID        =   [1005, '用户被禁用'];
    const USR_ID_NOEXIST    =   [1006, '用户id不存在'];
    const SCHOOL_ID_EXIST   =   [1007, '学号已存在,请勿重复注册'];
    const ERR_LOGIN_TYPE    =   [1008, '错误的登陆方式'];

    /**
     * Token检验异常
     */
    const ERR_EXPIRED       =   [1010, '登陆过期'];
    const ERR_NOTOKEN       =   [1011, '请登录'];
    const CHECK_FAILED      =   [1012, '身份验证失败，请重新登陆'];
    const NO_AUTH           =   [1013, '没有权限访问'];
    const ERR_UPDATE_TOKEN  =   [1014, '更新token失败'];
    const ILLEGAL_TOKEN     =   [1015, '非法token'];
    const EXIST_USER_INFO   =   [1016, '已存在该用户信息'];
    const ILLEGAL_REQUEST   =   [1017, '非法请求'];
    const REQUEST_TIME_OUT  =   [1018, '请求超时'];

    /**
     * 注册异常
     */
    const USR_EXISTED       =   [1020, '用户名已存在'];
    const ERR_PHONE         =   [1021, '手机号格式不正确'];

    /**
     * 上传异常
     *
     */

    const UPLOAD_ERROR      =   [1050, '上传出错'];
    const ERR_FILE_TYPE     =   [1051, '错误的文件格式'];
    const ERR_FILE_SIZE     =   [1052, '文件过大！'];
    const FILE_NOEXIST      =   [1053, '文件不存在!'];
}