<?php


namespace app\index\controller;


class Airticket extends Base
{
    public function index(){
        return $this->fetch();
    }
}