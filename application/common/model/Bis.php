<?php
namespace app\common\model;

use think\Model;

class Bis extends BaseModel
{

    /*
     *  通过状态获取商家数据
     * */
    public function getBisStatusList($status = 0)
    {
        $result = [
            'status'=>$status,
        ];

        $order = [
            'id'=>'desc',
        ];
        $res = $this->where($result)
                    ->order($order)
                    ->paginate(1);
        return $res;
    }

    public function getBisUserIsHave($data){

        $result = [
            'email'=>$data['email'],
            'faren'=>$data['faren'],
            'bank_user'=>$data['bank_user'],
            'bank_name'=>$data['bank_name'],
            'bank_info'=>$data['bank_info'],
            'name'=>$data['bisname']
        ];
        $res = $this->where($result)->value('id');
       return $res;

    }
}