<?php


namespace app\index\controller;




use think\Request;

class Airticket extends Base
{
    public function index(){

        return $this->fetch();
    }
}