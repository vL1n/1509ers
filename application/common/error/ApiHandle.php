<?php
/**
 * Created by PhpStorm.
 * User: lhw
 * Date: 2019/5/8
 * Time: 16:04
 */

namespace app\common\error;

use Exception;
use think\exception\Handle;

class ApiHandle extends Handle
{

    /**
     * 自定义异常处理必须实现render方法
     * @param Exception $e
     * @return \think\Response|\think\response\Json
     */
    public function render(Exception $e)
    {
        // 处理api请求异常
        if ($e instanceof ApiException) {
            return $e->getError();
        }

        // 其他错误交给系统处理
        return parent::render($e);
    }
}