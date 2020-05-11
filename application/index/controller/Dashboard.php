<?php


namespace app\index\controller;


class Dashboard extends Base
{
    public function index(){

        return $this->fetch();
    }
}