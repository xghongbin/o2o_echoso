<?php
namespace app\common\model;

use think\Model;

class BisAccount extends BaseModel
{

    public function getBisUserNameIsUse($data,$bisId){

        $result = [
            'bis_id'=>$bisId,
            'username'=>$data['username'],
            'is_main'=>1,
        ];
        $res = $this->where($result)->value('id');
        return $res > 0 ?true:false;

    }

}