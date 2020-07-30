<?php


namespace app\test\controller;


class Article extends Base
{
    public function index(){
        return $this->fetch();
    }
    public function read(){
        return $this->fetch();
    }
}