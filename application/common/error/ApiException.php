<?php
/**
 * Created by PhpStorm.
 * User: lhw
 * Date: 2019/5/4
 * Time: 19:23
 */

namespace app\common\error;

use Exception;
use app\api\jsonResponse\JsonResponse;
use Throwable;


/**
 * 自定义异常处理类
 * Class ApiException
 * @package app\api\err
 */
class ApiException extends Exception
{
    use JsonResponse;
    protected $ErrConst;

    /**
     * 改写构造函数
     * ApiException constructor.
     * @param array $ErrConst
     * @param Throwable|null $previous
     */
    public function __construct(array $ErrConst, Throwable $previous = null)
    {
        $this->ErrConst = $ErrConst;
        $code    = $ErrConst[0];
        $message = $ErrConst[1];
        parent::__construct($message, $code, $previous);
    }

    /**
     * 异常被捕获后调用的方法
     * @return string
     */
    public function getError(){
        return $this->jsonError($this->ErrConst);
    }


}
