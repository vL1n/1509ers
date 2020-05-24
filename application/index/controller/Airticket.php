<?php


namespace app\index\controller;



use app\common\handleSign\handleSign;
use think\Request;

class Airticket extends Base
{
    use handleSign;
    public function index(){

        return $this->fetch();
    }

    public function getAirTicket(Request $request){
        $info = $request->param();
        $data = $this->handleData($info);
        $airTicket = action('api/v1.Pythondata/getAirTicket',$data);
        return $airTicket;
    }
}