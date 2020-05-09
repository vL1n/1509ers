<?php
namespace app\index\controller;

use think\Controller;

class Index extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

    public function test(){
        return $this->fetch('/test');
    }
}
