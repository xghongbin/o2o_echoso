<?php
/**
 * Created by PhpStorm.
 * User: xghongbin
 * Date: 2017/4/4
 * Time: 0:41
 */
namespace app\common\model;

use think\Model;

class BaseModel extends Model
{
    protected $autoWriteTimestamp = true;
    public function add($data)
    {
        // 默认数据的新增
        $data['status']=0;
        $this->save($data);
        return $this->id;
    }

    // 后期看需求是否添加盐，这里是将ID于code做了个互换
    public function base64_decode_slat($code)
    {
        return base64_decode($code);
    }
}