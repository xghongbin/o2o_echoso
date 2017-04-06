<?php
/**
 * Created by PhpStorm.
 * User: xghongbin
 * Date: 2017/4/1
 * Time: 0:28
 */
namespace app\api\controller;
use think\Controller;
class Register extends Controller{

    protected $obj;
    public function _initialize()
    {
        $this->obj = model('Register');
    }

    //  检验 “商户地址” 精确度
    public function checkAddressPrecise()
    {
        //  数据校验
        if(!request()->isPost())
        {
            $this->error('非法操作');
        }

        $data = input('post.');

        //  商户地址通过Map.php获取经纬度
        $lnglat = \Map::getLngLat($data['address']);
        $lnglat = json_decode($lnglat,true);
        if(empty($lnglat) || $lnglat['status'] != 0 || $lnglat['result']['precise'] !=1)
        {
            return show(0,'success','商户地址无法获取数据，或者匹配的地址不精确');
        }
        return show(1,'success','精确度正常');
    }

    // 检验 “商户入驻”账号是否已存在
    public function checkUserName()
    {
        //  数据校验
        if(!request()->isPost())
        {
            $this->error('非法操作');
        }

        $data = input('post.');
        $bisId = model('Bis')->getBisUserIsHave($data);
        $haveUsername = model('BisAccount')->getBisUserNameIsUse($data,$bisId);

        if($bisId > 0 && !empty($bisId)){
            if($haveUsername){
                return show(0,'success',"此店铺已存在 '".$data['username']."' 管理员");
            }
                return show(0,'success',"此店铺已存在");
        }
                return show(1,'success','可以使用');
    }

}