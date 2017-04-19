<?php
namespace app\bis\controller;
use think\Controller;
/*
 * 登陆后台Bis商家管理后台页面，需要判断是否含有登陆页面所携带 session,
 * 才允许进入后台Bis商家管理后台页面，所以继承Base.php控制器
 * */
class Index extends Base
{
    public function index()
    {

        return $this->fetch();
    }

}

