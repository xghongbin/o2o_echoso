<?php
namespace app\admin\controller;
use think\Controller;

class Index extends Controller
{
    public function index()
    {

        return $this->fetch();
    }

    public function test()
    {
       \Map::getLngLat('白云文化广场');
    }

    public function map()
    {
        \Map::staticimage('白云文化广场');
    }

    public function welcome()
    {
        //\phpmailer\Email::send('350997333@qq.com','这里是标题','这里是内容');
        //return '发送邮件成功';
        return "欢迎来到主后台模块welcome方法";
    }
}

