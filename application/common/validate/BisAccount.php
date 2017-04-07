<?php
namespace app\common\Validate;
use think\Validate ;

class BisAccount extends Validate
{
    protected $rule=[
        ['username','require|max:20','用户名不能为空|用户名超过字数限制'],


    ];

    /* 场景规则设置 */
    protected $scene = [
        // 基本信息字段判断
        'chuekUserName'=>[
            'username',
        ]
    ];
}