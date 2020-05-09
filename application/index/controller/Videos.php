<?php


namespace app\index\controller;


use app\common\jsonResponse\JsonResponse;
use think\Request;

class Videos extends Base
{
    use JsonResponse;
    public function index(){
        return $this->fetch();
    }
}