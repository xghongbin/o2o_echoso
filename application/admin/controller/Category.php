<?php
namespace app\admin\controller;

use think\Controller;

class Category extends Controller
{
    protected $obj;
    public function index()
    {
        //  get[parent_Id]并传递获取子栏目数据
        $parentId = input('get.parent_id',0,'intval');

        $category = $this->obj->getFirstCategorys($parentId);

        return $this->fetch('',[
            'category'=>$category,
        ]);
    }

    public function welcome()
    {
        return "欢迎来到主后台模块welcome方法";
    }

    public function _initialize()
    {
        $this->obj = model('Category');
    }

    //  显示添加分类页面以及遍历一级栏目
    public function add()
    {
        $category = $this->obj->getNormalFirstCategory();
        return $this->fetch('',[
            'category'=>$category,
        ]);
    }

    //  保存一级栏目
    public function save()
    {

        // 严格数据验证
        if(!request()->isPost())
        {
            $this->error('非法操作');
        }
        $date = input('post.');
        $result = validate('category');

        //  判断是否含有隐藏域ID，有则为编辑
        if(!empty($date['id']))
        {
            // 编辑栏目前的数据校验：原一级分类含有二级子栏目时，不允许修改为其它栏目
            if($date['is_parent'] == '1')
            {
                $this->error("'".$date['name']."'含有子栏目,不允许编辑为其它子栏目");
            }
            // 编辑一级分类A下子栏目，判断若是最后一个子栏目，则将一级分类A is_parent=0 ,分配到的一级分类B is_parent=1;
            // 判断是否为最后一个子栏目
            $categoryNum = $this->obj->is_lastCategory($date);
            if($categoryNum)
            {
                // 为最后一个子栏目
                $this->obj->alterIsParentZero($date);
            }
            
            return $this->update($date);
        }



        if($result->scene('add')->check($date))
        {
            //数据验证完毕提交model层
            $res = $this->obj->add($date);
                if($res){
                    // 新增时栏目数据校验：若不是一级栏目分类，则is_parent字段判断为二级栏目
                    if($date['parent_id'] != 0)
                    {
                        $this->obj->update_parent($date);
                    }
                    $this->success('分类 "'.$date['name'].'" 新增成功');
                }else
                {
                    $this->error('分类 "'.$date['name'].'" 新增失败');
                }
        }else
        {
            $this->error($result->getError());
        }
    }

    /*
     * 生活服务分类编辑页面
     * */
    public function edit($id=0)
    {

        //  严格判断类型值
        if(intval($id) < 1)
        {
            $this->error('参数不合法 ');
        }

        // 获取内容，必须传的是主键，get()方法属于父类model方法，
        // 而不属于model\Category.php的方法[Category.php继承model基类]
        $category_date = $this->obj->get($id);

        $category = $this->obj->getNormalFirstCategory();

        return $this->fetch('',[
            'category'=>$category,
            'category_date'=>$category_date,
        ]);
    }

    /*
     *   更新操作，调用 Model基类的save()方法，
     *   而不是调用 common\model\Category.php 中的方法
     * */
    public function update($date)
    {

        $res = $this->obj->save($date,['id'=>intval($date['id'])]);
        if ($res)
        {
            // 若不是一级栏目分类，则is_parent字段判断为二级栏目
            if($date['parent_id'] != 0)
            {
                $this->obj->update_parent($date);
            }
            $this->success('更新成功');
        }else{
            $this->error('更新失败');
        }
    }


    /*
     * 排序功能
     * */
    public function listorder($listorder,$id)
    {
        $result = validate('category');
        $allres = [
            'listorder'=>$listorder,
            'id'=>$id
        ];

        if($result->scene('listorder')->check($allres)){
            $res = $this->obj->save(['listorder'=>$listorder],['id'=>$id]);
            if ($res)
            {
                //  $_SERVER['HTTP_REFERER']可以获取当前链接的上一个连接的来源地址，即链接到当前页面的前一页面的 URL 地址
                $this->result($_SERVER['HTTP_REFERER'],1,'success');
            }else{
                $this->result($_SERVER['HTTP_REFERER'],0,'error');
            }

        }else{
            $this->result($_SERVER['HTTP_REFERER'],0,'参数错误,必须是数字');
        }

    }

    /*
     * 修改状态
     * */
    public function status()
    {
        $date = input('get.');
        $validate = validate('Category');

        // 修改状态场景的验证
        if(!$validate->scene('status')->check($date))
        {
            $this->error($validate->getError());
        }

        if($date['state']=='del')
        {
            // 数据删除前的校验：一级栏目下含有子栏目不允许删除
            if($date['is_parent'] == '1' && $date['parent_id'] == '0')
            {
                $this->error('必须先删除子栏目');
            }

            // 若一级分类A下已经剩下最后的子栏目，则删除时，将一级栏目A is_parent = 0;
            $categoryNum = $this->obj->is_lastCategory($date);
            if($categoryNum)
            {
                // 为最后一个子栏目
                $this->obj->alterIsParentZero($date);
            }

        }elseif ($date['state']=='state_update'){
            // 更改发布状态前的校验
            if($date['is_parent'] == '1' && $date['parent_id'] == '0')
            {
                if($date['status']=='0')
                {
                    $this->error('先将子栏目设置为待审状态');
                }elseif($date['status']=='-1'){
                    $this->error('先删除子栏目才能删除一级分类');
                }

            }
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
     *  确认删除->标记为删除的分类
     * */
    public function dilition()
    {
        if(!request()->isGet())
        {
            $this->error('非法操作');
        }

        $num = $this->obj->delCategoryIsDel();
        if($num > 0)
        {
            return $this->success('删除成功，共计删除'.$num.'个分类');
        }else if($num == 0){
            return $this->error('不存在被标记为 "删除" 的分类');
        }else{
            return $this->error('删除失败');
        }


    }
}

