<?php

namespace app\index\controller;

use app\index\controller\Index;
use think\Request;

class Pyclimb extends Index
{
    public function index()
    {
        return $this->fetch('/pyindex');
    }

    public static function getData(Request $request){

        $info = $request->param();
        $dep = $info['dep'];
        $arr = $info['arr'];
        $date = $info['date'];
        $output = exec('python /home/wwwroot/1509/application/index/controller/phpConnector.py '.$dep.' '.$arr.' '.$date);
        //这里返回的是一个json数据，想要解析为数组可以用json_decode($output,1)
        return $output;
    }

    public function callPython(Request $request){

        $info = self::getData($request);
        $data = json_decode($info,1);
        $arrCityName = $data['data']['arrCityName'];
        $depCityName = $data['data']['depCityName'];
        $flight = $data['data']['flight'];
        return $this->fetch('/pyclimb',[
            'flightInfo' => $flight,
            'depCityName' => $depCityName,
            'arrCityName' => $arrCityName,
        ]);
    }

    public function callPythonJs(Request $request){

        $msg = '';
        $code = 0;
        $info = self::getData($request);
        $data = json_decode($info,1);
        if(isset($data['status'])){
            if($data['status'] == -1){
                $code = 0;
                $msg = $data['errorMsg'];
            }else{
                $code = 1;
            }
        }elseif(isset($data['error'])){
            if($data['error'] == -1){
                $code = 0;
                $msg = $data['errorMsg'];
            }else{
                $code = 1;
            }
        }else{
            $code = 1;
        }
        return ['code'=>$code,'info'=>$data,'msg'=>$msg];
    }

    public static function climbFlightForApp(Request $request){

        $info = self::getData($request);
        return $info;
    }
}
