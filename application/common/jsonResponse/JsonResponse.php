<?php
namespace app\common\jsonResponse;

use app\common\error\apiErrCode;

trait JsonResponse
{
    /**
     * 成功时返回的json
     * @param array $data
     * @return string
     */
    public function jsonSuccess($data = []){
        return $this->jsonData(apiErrCode::SUCCESS[0],apiErrCode::SUCCESS[1],$data);
    }

    /**
     * 出错时的返回，使用错误码常量
     * @param $ERR
     * @return string
     */
    public function jsonUnknownError(){
        return $this->jsonData(apiErrCode::ERR_UNKNOWN[0],apiErrCode::ERR_UNKNOWN[1]);
    }

    /**
     * @param string $code
     * @param string $msg
     * @param array $data
     * @return false|string
     */
    public function jsonData($code= '', $msg = '', $data = []){
        $content = [
            'code'  =>  $code,
            'msg'   =>  $msg,
            'data'  =>  $data
        ];
        return json_encode($content);
    }

    /**
     * @param string $code
     * @param string $msg
     * @param array $data
     * @return \think\response\Json
     */
    public function jsonInstanceData($code= '', $msg = '', $data = []){
        $content = [
            'code'  =>  $code,
            'msg'   =>  $msg,
            'data'  =>  $data
        ];
        return json($content);
    }
}