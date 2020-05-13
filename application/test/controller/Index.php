<?php


namespace app\test\controller;


class Index extends Base
{
    public function index(){
        return $this->fetch();
    }

}