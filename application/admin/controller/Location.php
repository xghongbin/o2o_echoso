<?php
namespace app\admin\controller;

use think\Controller;
use think\cache\Driver;
class Location extends Controller
{
    protected $obj;
    public function _initialize()
    {
        $this->obj = model('BisLocation');
    }

    public function index()
    {
        // 获取传送的 status=1
        if(!request()->isGet())
        {
            $this->error('非法操作');
        }
        $status = input('get.status',0,'intval');
        $bis = model('BisLocation')->getBisBranchStatusList($status);

        return $this->fetch('',[
            'bis'=>$bis,
        ]);
    }

    /*
     * 商户门店分店入驻申请
     * */
    public function apply()
    {
        //  分店信息
        $BisData = $this->obj->getBisBranchStatusList();

        //  总店信息
        //$HeadquartersData = model('Bis')->getBisStatusList(['id'=>$BisData['bis_id'],'is_main'=>1]);

        //dump($HeadquartersData);
        return $this->fetch('',[
            'BisData'=>$BisData,
        ]);
    }

    /*
     * 删除的商户
     * */
    public function dellist()
    {
        // 获取传送的 status=-1or2

        if(!request()->isGet())
        {
            $this->error('非法操作');
        }
        $status = input('get.status',-1,'intval');
        $bis = model('Bis')->getBisStatusList($status);

        return $this->fetch('',[
            'bis'=>$bis,
        ]);
    }

    /*
     * 审核不通过的商户
     * */
    public function faillist()
    {
        // 获取传送的 status=2

        if(!request()->isGet())
        {
            $this->error('非法操作');
        }
        $status = input('get.status',2,'intval');
        $bis = model('Bis')->getBisStatusList($status);

        return $this->fetch('',[
            'bis'=>$bis,
        ]);
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


        $result = model('BisAccount')->get(['bis_id'=>$date['id']]);
        $bisData = model('Bis')->get(['id'=>$date['id']]);

        //数据的保存
        $res = $this->obj->save(['status'=>$date['status']],['id'=>$date['id']]);
        if($res)
        {
            //  对另外2张表中的status标记为相同的状态
            model('BisAccount')->save(['status'=>$date['status']],['bis_id'=>$date['id']]);
            model('BisLocation')->save(['status'=>$date['status']],['bis_id'=>$date['id']]);

            /*
             * 根据action 判断是删除操作还是更改为其它状态
             * action:del   更改为删除的商户
             * */
            if(!empty($date['action']) && ($date['action'] == 'delBis' || $date['action'] == 'fail')){
                // 通知商户邮箱入驻申请处于删除阶段
                $bisCode = base64_encode($result->code);
                $bisId = base64_encode($date['id']);
                $url = request()->domain().url('bis/Register/waiting',['code'=>$bisId,'id'=>$bisCode]);
                $title = 'o2o入驻申请状态更改通知';
                $content =	"您提交的入驻申请状态发生了更新，您可以通过点击链接<a href='".$url."' targrt='_blank'>查看链接</a>查看审核状态";
                $bisData = model('Bis')->get(['id'=>$date['id']]);
                // 发送确认邮件
                \phpmailer\Email::send($bisData['email'],$title,$content);
            }


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

    /*
     * 显示地址图片
     * */
    public function map()
    {
        //  获取商户ID
        $bisId = input('get.id');
        // 总店信息
        $locationData = model('BisLocation')->get(['bis_id'=>$bisId,'is_main'=>1]);
        $address = $locationData->address;
        return \Map::staticimage($address);
    }

    /*
     * 确认删除 “商户入驻申请” 资料
     * */
    public function deltrue()
    {

        if(!request()->isGet())
        {
            $this->error('非法操作');
        }

        $date = input('get.');

        if(!empty($date['id'])){

            $bisAccountDate = model('BisAccount')->get(['bis_id'=>$date['id']]);
            $bisData = model('Bis')->get(['id'=>$date['id']]);

            /*
             * 根据action 判断是删除操作还是更改为其它状态
             * action:del   更改为删除的商户
             * */
            if(!empty($date['action']) && ($date['action'] == 'deltrue' || $date['action'] == 'delnoeamil')){
                if($date['action'] != 'delnoeamil')
                {
                    // 通知商户邮箱入驻申请处于删除阶段
                    $bisCode = base64_encode($bisAccountDate->code);
                    $bisId = base64_encode($date['id']);
                    $url = request()->domain().url('bis/Register/waiting',['code'=>$bisId,'id'=>$bisCode]);
                    $title = 'o2o入驻申请状态更改通知';
                    $content =	"您提交的入驻申请状态发生了更新，您可以通过点击链接<a href='".$url."' targrt='_blank'>查看链接</a>查看审核状态";
                    $bisData = model('Bis')->get(['id'=>$date['id']]);
                    // 发送确认邮件
                    \phpmailer\Email::send($bisData['email'],$title,$content);
                }

                // 删除所有申请入驻资料
                $unlinkImg1 = unlink('.'.$bisData['logo']);
                $unlinkImg2 = unlink('.'.$bisData['licence_logo']);
                if($unlinkImg1 && $unlinkImg2){
                    db('Bis')->delete($date['id']);
                    db('BisAccount')->where('bis_id',$date['id'])->delete();
                    db('BisLocation')->where('bis_id',$date['id'])->delete();
                    return $this->success('已删除该商户入驻申请所有信息');
                }else{
                    return $this->error('文件地址删除错误！');
                }
            }
        }
    }
}

