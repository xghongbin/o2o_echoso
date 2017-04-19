<?php
namespace app\bis\controller;
use think\Controller;

class Login extends Controller
{
    public function index()
    {

        if(request()->isPost())
        {
            $data = input('post.');
            // 严格判断,validate验证字段合法性
                $bisAccount = validate('BisAccount');

                $checkField = $bisAccount->scene('checklogin')->check($data);
                if(!$checkField)
                {
                    $this->error($bisAccount->getError());
                }

            //  模块判断用户信息，获取用户相关信息
                $ret = model('BisAccount')->get(['username'=>$data['username']]);

            //  判断商户是否审核通过
                if(!empty($ret) && $ret->status != 1)
                {
                    $this->error('商户不存在或者商户未被审核通过');
                }
            //  验证用户密码正确性
                if($ret->password != md5($ret->code.$data['password']))
                {
                    $this->error('密码错误');
                }
            //  密码正确则更新用户基本信息
                model('BisAccount')->updateByIdInfo(['last_login_time'=>time(),'last_login_ip'=>request()->ip()],$ret->id);
            // 记录状态，保存session，需要到 application\config.php 配置文件中对session初始化
                session('bisAccount',$ret,'bis');
            return $this->success('登陆成功',url('index/index'));
        }else{
            //md5($data['password'].$data['code']
            echo md5('3562qweasd715').'------admin1'.'<br/>';
            echo md5('6706qweasd715').'------admin';
            //  判断是否已经登陆过
            $account = session('bisAccount','','bis');
            if($account && $account->id)
            {
                // redirect() 实现页面的重定向功能
                return $this->redirect(url('index/index'));
            }
            return $this->fetch();
        }

    }

    public function logout()
    {
        //  清除session
        session(null,'bis');
        //  跳转登陆页面
        $this->redirect(url('login/index'));
    }

}

