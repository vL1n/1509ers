<?php
namespace app\common\handleSign;

use app\common\error\apiErrCode;
use think\facade\Config;

trait handleSign
{
    /**
     * @param array $data
     * @return array
     */
    public function handleData($data=[]){

        $secret = Config::get('secret');
        $time = time();
        ksort($data);
        $data['time'] = $time;
        $req = http_build_query($data);
        $sign = md5($req.$secret);
        $data['sign'] = $sign;
        return $data;
    }
}
