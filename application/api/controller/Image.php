<?php
/**
 * Created by PhpStorm.
 * User: xghongbin
 * Date: 2017/4/1
 * Time: 0:28
 */
namespace app\api\controller;
use think\Controller;
use think\Request;
class Image extends Controller{

    public function upload(){
        /* 由于继承了think\Controller 直接可以使用简化调用
         * 而不需要
         * $file =  Request::instance()->file('file');
         * thinkphp\library\think\request.php 中包含获取上传的文件信息的方法， function file()
         */
        $file = $this->request->file('file');
        //$file =  Request::instance()->file('file');
        //  给定目录存放上传文件
        $info = $file->move('upload');

        if($info && $info->getPathname()){
                // 图片上传成功
                // 参数三，返回地址给前端调用显示
            return show(1,'success',config('__STATIC__').'/'.$info->getPathname());
        }
            return show(0,'upload error');

    }

}