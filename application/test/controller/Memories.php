<?php


namespace app\test\controller;


use think\Request;

class Memories extends Base
{
    public function index(){
        $this->assign('user_id','');
        return $this->fetch();
    }
}