<?php
namespace app\common\model;

use think\Model;

class City extends Model
{
    //  获取一级城市下的二级城市信息
    public function getNormalCitysByParentId($parentId = 0)
    {
        $result = [
            'status'=>1,
            'parent_id'=>$parentId,
        ];
        $order = [
            'id'=>'desc',
        ];
        $res = $this->where($result)
            ->order($order)
            ->select();
        return $res;
    }

    //  获取指定一级城市名称
    public function getNormalCitysByName($cityId = 0)
    {
        $result = [
            'status'=>1,
            'id'=>$cityId,
        ];
        $order = [
            'id'=>'desc',
        ];
        $res = $this->where($result)
                    ->find();
        return $res;
    }
}