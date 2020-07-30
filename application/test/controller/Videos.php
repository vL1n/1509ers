<?php


namespace app\test\controller;


class Videos extends Base
{
    public function index(){
        return $this->fetch();
    }
}