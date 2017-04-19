<?php
namespace app\bis\controller;
use think\Controller;
//  商家入驻申请页面的form提交处理
class Register extends Controller
{
    public function index()
    {
        $citys = model('City')->getNormalCitysByParentId();
        $categorys = model('Category')->getNormalCategorysByParentId();
        return $this->fetch('',[
            'citys'=>$citys,
            'categorys'=>$categorys,
        ]);
    }


    public function add()
    {
        //  数据校验
        if(!request()->isPost())
        {
            $this->error('非法操作');
        }

        $data = input('post.');
        $validate = validate('Bis');
        //  基本信息验证
        if(!$validate->scene('basic_info')->check($data))
        {
            $this->error($validate->getError());
        }

        //  商户地址通过Map.php获取经纬度
        $lnglat = \Map::getLngLat($data['address'],0);

        if(empty($lnglat) || $lnglat['status'] != 0 || $lnglat['result']['precise'] !=1)
        {
                $this->error('商户地址无法获取数据，或者匹配的地址不精确');
        }

        //  总店相关信息验证
        if(!$validate->scene('basic_info')->check($data))
        {
            $this->error($validate->getError());
        }

        //  账户相关信息验证
        if(!$validate->scene('account_info')->check($data))
        {
            $this->error($validate->getError());
        }

        //  错误信息 String
        $errorInfo = '';

        //  商户基本信息入库
        $bisData = [
            'name'=>$data['name'],
            'city_id'=>$data['city_id'],
            'city_path'=>empty($data['se_city_id']) ? $data['city_id'] : $data['city_id'].','.$data['se_city_id'],
            'logo'=>$data['logo'],
            'licence_logo'=>$data['licence_logo'],
            'description'=>$data['description'],
            'bank_info'=>$data['bank_info'],
            'bank_name'=>$data['bank_name'],
            'bank_user'=>$data['bank_user'],
            'faren'=>$data['faren'],
            'faren_tel'=>$data['faren_tel'],
            'email'=>$data['email']
        ];

        $bisId = model('Bis')->add($bisData);
        if (!$bisId)
        {
            $errorInfo = '总店信息错误';
            $this->error('申请失败，原因:'.$errorInfo);
        }

        //  总店相关入库
        $data['cat'] = '';
        //  所属分类判断
        if(!empty($data['se_category_id']))
        {
            $data['cat']=implode('|',$data['se_category_id']);
        }
        $locationData = [
            'bis_id'=>$bisId,// 商户表外键
            'name'=>$data['name'],
            'logo'=>$data['logo'],
            'contact'=>$data['contact'],
            'category_id'=>$data['category_id'],
            'category_path'=>$data['category_id'].','.$data['cat'],
            'city_id'=>$data['city_id'],
            'city_path'=>empty($data['se_city_id']) ? $data['city_id'] : $data['city_id'].','.$data['se_city_id'],
            'address'=>$data['address'],
            'tel'=>$data['tel'],
            'bank_info'=>$data['bank_info'],
            'open_time'=>$data['open_time'],
            'content'=>$data['content'],
            'is_main'=>1,// 标识为总店
            'xpoint'=>empty($lnglat['result']['location']['lng'])?'':$lnglat['result']['location']['lng'],
            'ypoint'=>empty($lnglat['result']['location']['lat'])?'':$lnglat['result']['location']['lat']
        ];
        $locationId = model('BisLocation')->add($locationData);

        if (!$locationId){
            $accountResult = Model('BisAccount')->get(['username'=>$data['username']]);
            if($accountResult){
                $errorInfo = '该用户存在，请重新分配';
                $this->error('申请失败，原因:'.$errorInfo);
            }
        }

        //  账户相关信息入库
        //  密码slat加密
        $data['code'] = mt_rand(100,10000);
        $accountData = [
            'bis_id'=>$bisId,// 商户表外键
            'username'=>$data['username'],
            'code'=>$data['code'],
            'password'=>md5($data['password'].$data['code']),
            'is_main'=>1,// 标识为总店总管理员
        ];
        $accountId = model('BisAccount')->add($accountData);
        if (!$accountId)
        {
            $errorInfo = '商户基本信息错误';
            $this->error('申请失败，原因:'.$errorInfo);
        }else{
            //  账户信息正确则发送邮件提醒
            // title和content 可从另外的邮件发送表中取出模板，在模板中去定义并从数据库中获取
            // URL 是点击跳转的链接地址，第三方发送邮件的地址知识点
            $bisCode = base64_encode($data['code']);
            $bisId = base64_encode($bisId);
            $url = request()->domain().url('bis/Register/waiting',['code'=>$bisId,'id'=>$bisCode]);
            $title = 'o2o入驻申请通知';
            $content =	"您提交的入驻申请需等待平台方审核，您可以通过点击链接<a href='".$url."' targrt='_blank'>查看链接</a>查看审核状态";

            // 发送确认邮件
            \phpmailer\Email::send($data['email'],$title,$content);
            $this->success('申请成功',url('register/waiting',['code'=>$bisId,'id'=>$bisCode]));
        }

    }// add

    /*
     * 邮件确认跳转页
     * @param   id其实是code
     * @param   code其实才是真的Id
     * */
    public function waiting($id,$code)
    {
        if(empty($id) || empty($code))
        {
            $this->error('非法操作',url('index/index/index'));
        }

        // 验证盐加密
        $id = model('Bis')->base64_decode_slat($code);
        $detail = model('Bis')->get($id);

        return $this->fetch('',[
            'detail'=>$detail,
        ]);

    }


}












