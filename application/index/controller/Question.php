<?php


namespace app\index\controller;


class Question extends Base
{
    public function index(){
        return $this->fetch();
    }
}