<?php
namespace app\common\model;

use think\Model;

class Category extends Model
{
    protected $autoWriteTimestamp = true;
    public function add($date)
    {
        // 默认数据的新增
        $date['status']=1;
        //$data['create_time']= time();

        return $this->save($date);
    }

    // 二级栏目的添加会造成一级栏目is_parent = 1，列表才会显示"获取子栏目"
    public function update_parent($date){

        if($date['parent_id'] != 0)
        {
            $date = [
                'id'=>$date['parent_id'],
                'is_parent'=>1,
                'parent_id'=>$date['parent_id'],
            ];

           return $this->save($date, ['id' => intval($date['parent_id'])]);
        }
    }

    //  获取添加分类页面：分类栏目
    public function getNormalFirstCategory()
    {
        $result = [
            'status'=>1,
            'parent_id'=>0,
        ];
        $order = [
            'id'=>'esc',
        ];
      return  $this->where($result)
            ->order($order)
            ->select();
    }

    //  获取生活服务分类一级栏目，status ！= -1(不等于删除状态)
    public function getFirstCategorys($parentId = 0)
    {
        $result = [
            'status'=>['neq',-1],
            'parent_id'=>$parentId,
        ];
        $order = [
            'listorder'=>'desc',
            'id'=>'desc',
        ];
         $result = $this->where($result)
            ->order($order)
            ->paginate();
        //  获取查看最后一次SQL语句得输出
        //echo $this->getLastSql();
        return $result;
    }

    //  返回是否为最后一个子栏目
    public function is_lastCategory($date)
    {

        $result = [
            'id'=>$date['id'],
        ];
        $theParentId = $this->where($result)
                            ->value('parent_id');

        $result = [
            'parent_id'=> $theParentId,
            'is_parent'=>['neq',1],
            'status'=>['neq',-1]
        ];
        $num = $this->where($result)
                    ->count('id');

        return $num<=1?true:false;
    }

    //  若一级分类下的子栏目小于等于1，则将此一级分类的is_parent = 0
    public function alterIsParentZero($date)
    {
        $result = [
            'id'=>$date['id'],
        ];
        $theParentId = $this->where($result)
                            ->value('parent_id');
        $date = [
            'is_parent'=> 0,
        ];

        return $this->save($date, ['id' => intval($theParentId)]);
    }

    //  获取一级栏目分类信息，status=1
    public function getNormalCategorysByParentId($parentId = 0)
    {
        $result = [
            'status'=>1,
            'parent_id'=>$parentId,
        ];
        $order = [
            'id'=>'desc',
        ];
        return  $this->where($result)
            ->order($order)
            ->select();
    }
}