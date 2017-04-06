<?php
namespace app\index\controller;
use think\Controller;
class Categroy extends Controller
{
    public function index()
    {
       return $this->fetch();
    }
    public function add()
    {
        return $this->fetch();
    }
}
