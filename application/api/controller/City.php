<?php
/**
 * Created by PhpStorm.
 * User: xghongbin
 * Date: 2017/4/1
 * Time: 0:28
 */
namespace app\api\controller;
use think\Controller;
class City extends Controller{

    protected $obj;
    public function _initialize()
    {
        $this->obj = model('City');
    }

    /*
     * register\index.html页面指定抛送的 URL 地址，
     * 再这里将数据返回给common.js，进行回调判断
     * */
    public function getCitysByParentId(){
        $city_id = input('post.id');
        if(!intval($city_id))
        {
             $this->error('ID不合法');
        }

        $citys = $this->obj->getNormalCitysByParentId($city_id);


        /*
         *  2种方法将获取的二级城市数据，传递给JS
         * 1：$this->result('','','');
         * 2: common.php公共文件定义一个将二级城市数据返回给JS的show()方法
         * */
        if(!$citys)
        {
            return show(0,'error');
        }
        return show(1,'success',$citys);

    }
}