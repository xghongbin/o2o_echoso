<?php
/**
 * Created by PhpStorm.
 * User: xghongbin
 * Date: 2017/3/27
 * Time: 23:08
 */
namespace app\admin\validate;
use think\Validate;
class Category extends Validate
{
    protected $rule=[
        ['name','require|max:10','分类名称不能为空|分类名称不能超过10个字符'],
        ['parent_id','number','分类栏目必须为数字'],
        ['id','number','id必须是数字'],
        ['status','number|in:-1,0,1','状态必须是数字|状态范围不合法'],
        ['listorder','number','排序必须是数字']
    ];

    /* 场景规则设置 */
    protected $scene = [
        'add'=>['name','parent_id'],//添加场景的字段判断
        'listorder'=>['id','listorder'],//排序场景的字段判断
        'update'=>['id','name'],//编辑场景的字段判断
        'status'=>['id','starus'],//修改状态
    ];
}