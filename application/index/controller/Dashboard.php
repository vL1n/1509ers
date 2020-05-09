<?php


namespace app\index\controller;


class Dashboard extends Base
{
    public function index(){

        return $this->fetch();
    }

    public function testHtml(){

        return $this->fetch();
    }
}