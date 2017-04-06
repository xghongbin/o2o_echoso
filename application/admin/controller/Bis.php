<?php
namespace app\admin\controller;

use think\Controller;

class Bis extends Controller
{
    protected $obj;
    public function _initialize()
    {
        $this->obj = model('Bis');
    }

    public function index()
    {
        //  get[parent_Id]并传递获取子栏目数据
        //$parentId = input('get.parent_id',0,'intval');

        //$category = $this->obj->getFirstCategorys($parentId);

        return $this->fetch('',[
            //'category'=>$category,
        ]);
    }

    /*
     * 商户入驻申请
     * */
    public function apply()
    {
        $BisData = model('Bis')->getBisStatusList();

        return $this->fetch('',[
            'BisData'=>$BisData,
        ]);
    }

    /*
     * 删除的商户
     * */
    public function delapply()
    {
        return $this->fetch();
    }

    /*
     * 修改状态
     * */
    public function status()
    {
        $date = input('get.');
        $validate = validate('Bis');

        // 修改状态场景的验证
        if(!$validate->scene('status')->check($date))
        {
            $this->error($validate->getError());
        }

        //数据的保存
        $res = $this->obj->save(['status'=>$date['status']],['id'=>$date['id']]);
        if($res)
        {
            $this->success('状态更新成功');
        }else{
            $this->error('状态更新失败');
        }

    }

    /*
     * 生活服务分类编辑页面
     * */
    public function detail()
    {

        $bisId = input('get.id');
        //  严格判断类型值
        if(intval($bisId) < 1)
        {
            $this->error('参数不合法 ');
        }

        if(empty($bisId))
        {
            $this->error('参数错误');
        }

        // 获取内容，必须传的是主键，get()方法属于父类model方法，
        // 而不属于model\Category.php的方法[Category.php继承model基类]
        // 获取商户数据 ,由于商户表和其他2张表都是有相关联的bisId,
        // 可以使用tp5的关联操作来进行处理，但例子并没有
        $bisData = $this->obj->get($bisId);

        //  获取一级城市的数据
        $citys = model('City')->getNormalCitysByName($bisData->city_id);
        //  获取一级栏目的数据
        $categorys = model('Category')->getNormalCategorysByParentId();

        // 总店信息
        $locationData = model('BisLocation')->get(['bis_id'=>$bisId,'is_main'=>1]);

        // 总店账户信息
        $accountData = model('BisAccount')->get(['bis_id'=>$bisId,'is_main'=>1]);


        return $this->fetch('',[
            'citys'=>$citys,
            'categorys'=>$categorys,
            'bisData'=>$bisData,
            'locationData'=>$locationData,
            'accountData'=>$accountData,
            'bisId'=>$bisId,
        ]);
    }

    public function map()
    {
        //  获取商户ID
        $bisId = input('get.id');
        // 总店信息
        $locationData = model('BisLocation')->get(['bis_id'=>$bisId,'is_main'=>1]);
        $address = $locationData->address;
        return \Map::staticimage($address);
    }
}

