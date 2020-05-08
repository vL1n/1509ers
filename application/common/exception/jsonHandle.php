<?php


namespace app\common\exception;


use app\common\jsonResponse\JsonResponse;
use Exception;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ValidateException;

class jsonHandle extends Handle
{
    use JsonResponse;

    public function render(Exception $e)
    {
        // 参数验证错误
        if ($e instanceof ValidateException) {
            return $this->jsonData(422,$e->getMessage());
//            return json($e->getError(), 422);
        }

        // 请求异常
        if ($e instanceof HttpException && request()->isAjax()) {
            return $this->jsonData($e->getStatusCode(),$e->getMessage());
//            return response($e->getMessage(), $e->getStatusCode());
        }
        return parent::render($e);
    }
}