<?php


namespace app\index\controller;


use app\common\handleSign\handleSign;
use think\Request;

class Question extends Base
{
    use handleSign;
    public function index(){
        return $this->fetch();
    }

    public function searchQuestion(Request $request){
        $info = $request->param();
        $data = $this->handleData($info);
        $q = action('api/v1.Pythondata/searchQuestion',$data);
        return $q;
    }
}