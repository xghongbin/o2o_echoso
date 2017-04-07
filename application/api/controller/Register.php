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

        //  ”店铺申请入驻“ 字段验证不能为空
        foreach ($data as $key=>$value){
            switch($key){
                case 'faren':
                    $key = '法人';
                    break;
                case 'username':
                    $key = '商户名称';
                    break;
                case 'email':
                    $key = '邮箱';
                    break;
                case 'bank_user':
                    $key = '开户行姓名';
                    break;
                case 'bank_name':
                    $key = '开户行名称';
                    break;
                case 'bank_info':
                    $key = '银行账号';
                    break;
                case 'name':
                    $key = '商户名称';
                    break;
            }
            if($value == ''){
                return show(0,'error',$key."不能为空");
            }
        }

        $checkDate = validate('Bis');
        $res = $checkDate->scene('chuekUserName')->check($data);

        //  先验证 Bis表 的基本信息字段是否正确
        if($checkDate->scene('chuekUserName')->check($data)){
            //  再进行 bis_account表字段的信息验证
            $checkUsername = validate('BisAccount');
            if($checkUsername->scene('chuekUserName')->check($data)){

                //  最后再进行返回判断数据给JS
                $bisId = model('Bis')->getBisUserIsHave($data);

                $haveUsername = model('BisAccount')->getBisUserNameIsUse($data,$bisId);

                if($bisId > 0 && !empty($bisId)){
                    if($haveUsername){
                        return show(0,'error',"此店铺已存在 '".$data['username']."' 管理员");
                    }
                    return show(0,'error',"此店铺已存在");
                }
                return show(1,'success','可以使用');

            }else{
                $this->error($checkUsername->getError());
            }
        }else{
            $this->error($checkDate->getError());
        }
        

    }

}