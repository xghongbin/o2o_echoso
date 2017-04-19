<?php
/**
 * Created by PhpStorm.
 * User: xghongbin
 * Date: 2017/4/8
 * Time: 12:41
 */
namespace app\bis\controller;
use think\Controller;
/*
 *  类Base用于判断Bis模块是否已经登陆，session是否已存在，只有在login/index登陆的页面才会设置session，
 *  所以后台页面一律都要继承此文件，当然，不同模块之间的Base.php文件和session作用域不同，区别对待
 * */
class Base extends Controller{
    //  优化知识点：getLoginUserSession() 被执行2次的优化
    public $account;

    //  初始化判断是否登陆
    public function _initialize()
    {
        //  判断是否登陆
        $isLogin = $this->isLogin();
        //  判断是否已经登陆获得Session，否则跳转Bis模块登陆页面
        if(!$isLogin){
            $this->redirect('login/index');
        }
    }

    //  登陆方法
    public function isLogin()
    {
        //  是否登陆，判断session是否存在
        $userSession = $this->getLoginUserSession();
        //  判断session中ID是否存在且session不为空，返回boolean值
        if($userSession && $userSession->id)
        {
            return true;
        }
        return false;
    }

    //  判断是否存在session
    public function getLoginUserSession()
    {
        if(!$this->account)
        {
            $this->account = session('bisAccount','','bis');
        }
        return $this->account;
    }
}