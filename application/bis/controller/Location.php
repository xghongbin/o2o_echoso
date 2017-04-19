<?php
namespace app\bis\controller;
use think\Controller;
/*
 * 登陆后台Bis商家管理后台页面，需要判断是否含有登陆页面所携带 session,
 * 才允许进入后台Bis商家管理后台页面，所以继承Base.php控制器
 * */
class Location extends Base
{
    protected $obj;
    public function _initialize()
    {
        $this->obj = model('BisLocation');
    }

    /*
     * 商户中心-门店列表
     * */
    public function index()
    {
        // 获取传送的 status=1
        if(!request()->isGet())
        {
            $this->error('非法操作');
        }
        // 默认状态为0
        // 必须是登录者的Bis_id
        $bisId  = $this->getLoginUserSession()->bis_id;
        $bis = model('BisLocation')->getBranchBisLocation($bisId);

        return $this->fetch('',[
            'bis'=>$bis,
        ]);
    }

    /*
     * 商户中心-新增门店
     * */
    public function add()
    {
        //  判断是否为post提交添加分店的数据
        if(request()->isPost())
        {
            //  门店入库操作
            $data = input('post.');
            $validate = validate('Bis');

            //  分店基本信息验证
            if(!$validate->scene('branchInfo')->check($data))
            {
                $this->error($validate->getError());
            }

            //  错误信息 String
            $errorInfo = '';

            //  分店相关入库
            $data['cat'] = '';
            //  所属分类判断
            if(!empty($data['se_category_id']))
            {
                $data['cat']=implode('|',$data['se_category_id']);
            }

            //  商户地址通过Map.php获取经纬度
            $lnglat = \Map::getLngLat($data['address'],0);


            if(empty($lnglat) || $lnglat['status'] != 0 || $lnglat['result']['precise'] !=1)
            {
                $this->error('商户地址无法获取数据，或者匹配的地址不精确');
            }

            // 获取登陆账号信息中的bis_id
            $account_bisId = $this->getLoginUserSession()->bis_id;

            $locationData = [
                'bis_id'=>$account_bisId,// 商户表外键
                'name'=>$data['name'],
                'logo'=>$data['logo'],
                'contact'=>$data['contact'],
                'category_id'=>$data['category_id'],
                'category_path'=>$data['category_id'].','.$data['cat'],
                'city_id'=>$data['city_id'],
                'city_path'=>empty($data['se_city_id']) ? $data['city_id'] : $data['city_id'].','.$data['se_city_id'],
                'address'=>$data['address'],
                'tel'=>$data['tel'],
                'open_time'=>$data['open_time'],
                'content'=>$data['content'],
                'is_main'=>0,// 标识为分店
                'xpoint'=>empty($lnglat['result']['location']['lng'])?'':$lnglat['result']['location']['lng'],
                'ypoint'=>empty($lnglat['result']['location']['lat'])?'':$lnglat['result']['location']['lat']
            ];
            $locationId = model('BisLocation')->add($locationData);

            if (!$locationId){
                  return  $this->error('分店申请失败');
            }else{
                  return  $this->success('分店申请成功');
            }

        }else{

            $citys = model('City')->getNormalCitysByParentId();
            $categorys = model('Category')->getNormalCategorysByParentId();
            return $this->fetch('',[
                'citys'=>$citys,
                'categorys'=>$categorys,
            ]);
        }
    }

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


        return $this->fetch('',[
            'citys'=>$citys,
            'categorys'=>$categorys,
            'bisData'=>$bisData,
            'bisId'=>$bisId,
        ]);
    }

    /*
     * 显示地址图片
     * */
    public function map()
    {
        //  获取商户ID
        $bisId = input('get.id');
        // 分店信息
        $locationData = model('BisLocation')->get(['id'=>$bisId,'is_main'=>0]);
        $address = $locationData['address'];
        return \Map::staticimage($address);
    }
}

