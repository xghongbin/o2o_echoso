<?php
/**
 * Created by PhpStorm.
 * User: xghongbin
 * Date: 2017/3/22
 * Time: 23:15
 */
namespace app\index\controller;
use think\Controller;

class User extends Controller{

    public function login()
    {
        return $this->fetch();
    }

    public function register()
    {
        return $this->fetch();
    }

}