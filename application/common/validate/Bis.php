<?php
namespace app\common\Validate;
use think\Validate ;

class Bis extends Validate
{
/*
 * Array
(
    [name] => 商户名称
    [city_id] => 1
    [se_city_id] => 2
    [logo] => /upload\20170403\877efa8ec384218c25ad1446069e4e87.png
    [licence_logo] => /upload\20170403\f69534ba6dcf2822f9ea9b271068d00c.png
    [bank_info] => 银行账号
    [bank_name] => 开户行名称
    [bank_user] => 开户行姓名
    [faren] => 法人
    [faren_tel] => 法人电话
    [email] => 邮箱
    [tel] => 电话
    [contact] => 联系人:
    [category_id] => 8
    [se_category_id] => Array
        (
            [i] => 18
        )

    [address] => 商户地址
    [open_time] => 营业时间
    [username] => user
    [password] => password
    [description] => <p><span style="color: rgb(85, 85, 85); font-family: &#39;Microsoft Yahei&#39;, &#39;Hiragino Sans GB&#39;, &#39;Helvetica Neue&#39;, Helvetica, tahoma, arial, &#39;WenQuanYi Micro Hei&#39;, Verdana, sans-serif, 宋体; font-size: 14px; line-height: 22.4px; text-align: right; background-color: rgb(255, 255, 255);">商户介绍</span></p>
    [content] => <p><span style="color: rgb(85, 85, 85); font-family: &#39;Microsoft Yahei&#39;, &#39;Hiragino Sans GB&#39;, &#39;Helvetica Neue&#39;, Helvetica, tahoma, arial, &#39;WenQuanYi Micro Hei&#39;, Verdana, sans-serif, 宋体; font-size: 14px; line-height: 22.4px; text-align: right; background-color: rgb(255, 255, 255);">门店简介</span></p>
)
     * */
    protected $rule=[
        ['name','require|max:20','商户名称不能为空|商户名称名称不能超过20个字符'],
        ['city_id','require|number','城市必须选择|非法城市数据'],
        ['se_city_id','require|number','二级城市必须选择|非法二级城市数据'],
        ['licence_logo','require','必须上传营业执照'],
        ['bank_info','require|number|length:15,19','必须上传银行账号|银行账号必须是数字|银行账户长度错误'],
        ['bank_name','require','开户行名称不能为空'],
        ['bank_user','require','开户行姓名不能为空'],
        ['faren','require','法人不能为空'],
        ['faren_tel','require','法人电话不能为空'],
        ['email','email','邮箱错误'],
        ['contact','require','联系人不能为空'],
        ['category_id','require|number','请选择商品分类|商品分类错误'],
        ['address','require','商户地址必须填写'],
        ['open_time','require','营业时间必须填写'],
        ['username','require','用户名必须填写'],
        ['password','require|alphaNum|length:8,15','密码必须填写|密码复杂度不够|密码长度不够'],
        ['status','number|in:-1,0,1','状态必须是数字|状态范围不合法'],
        ['id','number','ID必须是数字'],
        //['description','length:15,30','商户介绍字数太少'],
        //['content','length:15,30','门店简介字数太少']

    ];

    /* 场景规则设置 */
    protected $scene = [
        // 基本信息字段判断
        'basic_info'=>[
            'name',
            'city_id',
            'se_city_id',
            'licence_logo',
            //'description',
            //'content',
            'bank_info',
            'bank_name',
            'bank_user',
            'faren',
            'faren_tel',
            'email',
        ],
        // 总店信息字段判断
        'headOffice_info'=>[
            'contact',
            'category_id',
            'address',
            'open_time',
        ],
        // 账户信息字段判断
        'account_info'=>[
            'username',
            'password',
        ],
        'status'=>[
            'id',
            'status',
        ],
    ];
}