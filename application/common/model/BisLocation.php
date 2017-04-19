<?php
namespace app\common\model;

use think\Model;

class BisLocation extends BaseModel
{

    /*
     * 获取分店信息
     * @param string 0/1
     * @param int  登陆者账号总店bis表ID
     * */
    public function getBranchBisLocation($bisId)
    {
        $result = [
            'bis_id'=>$bisId,
            'is_main'=>0,
        ];
        $order = [
            'id'=>'desc',
        ];

      return   $this->where($result)->order($order)->paginate(1);
    }
    
    /*
     *  通过状态获取分店列表数据
     *  并且总店必须status为1的状态
     * */
    public function getBisBranchStatusList($status = 0)
    {
        $result = [
            'status'=>$status,
            'is_main'=>0,
        ];

        $order = [
            'id'=>'desc',
        ];
        $res = $this->where($result)
            ->order($order)
            ->paginate(1);
        return $res;
    }


}