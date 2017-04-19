<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/*
 * 格式化文案内容
 * */
function status_defintion($date)
{

        switch($date)
        {
            case 1:
                $status = "<span class='btn btn-success radius'>正常</span>";
                break;
            case 0:
                $status = "<span class='btn btn-warning radius'>待审</span>";
                break;
            case -1:
                $status = "<span class='btn btn-warning radius'>删除</span>";
                break;
            case 2:
                $status = "<span class='btn btn-danger radius'>不通过</span>";
                break;
            default:
                $status = "<span class='btn btn-danger radius'>未定义错误</span>";
        }

        return $status;

}

/*
 *  封装CURL，https请求  GET/POST
 *  @param  string 请求的地址
 *  @param  array  POST是需要向服务器提交数据的
 *  @param  string 需要返回的是数组[0]还是json[1] 默认为返回json
 *  例子：$data = array("filename"=>"@img/ecshso.png");
 * */
function http_request($url,$data = null,$isArray=0)
{
    //  初始化CURL
    $ch = curl_init();

    //  设置传输选项
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);// 将页面以文件流的的形式保存，因为并非获取URL地址中的所有数据，只获取部分数据
    curl_setopt($ch,CURLOPT_HEADER,0);// 将head头部数出来，0 不需要将头部head输出，1 需要将头部head输出

    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);

    //  由于POST是主要用于向服务器提交数据，所以需要用到以下参数配置
    if(!empty($data)){
        curl_setopt($ch,CURLOPT_POST,1);//  模拟POST请求向服务器提交数据
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);//  POST提交的内容
    }

    //  执行并获取结果
    $outopt = curl_exec($ch);
    //  关闭CURL
    curl_close($ch);
    if($isArray){
        return $outopt;
    }else{
        return json_decode($outopt,true);
    }

}

// 商户入驻申请文案
function bisRegister($status)
{
    if($status == 1){
        $str = "入驻申请成功";
    }elseif($status == 0){
        $str = "待审核，审核后平台方会自动邮件通知，请注意邮件查收";
    }elseif($status == 2){
        $str = "非常抱歉，您提交的材料不符合条件，请重新提交";
    }else{
        $str = "该申请已被删除";
    }
    return $str;
}


/*
 * 共用分页函数
 * @param  object 对象
 * */
function pagination($obj)
{
    if(!$obj)
    {
        return '';
    }
    return '<div class="cl pd-5 bg-1 bk-gray mt-20 category-page">'.$obj->render().'</div>';
}


/*
 * 返回二级城市名称
 * @param   传递  city_path：4,5
 * ','分割获取二级城市ID
 * */
function getSeCityName($path)
{
    if(empty($path))
    {
        return '';
    }
    //由于city_path 数据库中是4,5  有可能存在二级城市，也有可能不存在二级城市，
    //需要判断分割
    if(preg_match('/,/',$path))
    {
        $cityPath = explode(',',$path);
        $cityId = $cityPath['1'];
    }else{
        //  若没有',',则表示没有选择二级城市，city_path:4
        $cityId = $path;
    }
    $cityName = model('City')->get($cityId);
    return $cityName->name;

}

/*
 * 获取设置的二级城市名称
 * */
function getSetPathName($path,$setName)
{
    if(empty($path))
    {
        return '';
    }
    //由于city_path 数据库中是4,5  有可能存在二级城市，也有可能不存在二级城市，
    //需要判断分割
    if(preg_match('/,/',$path))
    {
        $setPath = explode(',',$path);
        $setId = $setPath['1'];
    }else{
        //  若没有',',则表示没有选择二级城市，city_path:4
        $setId = $path;
    }
    $setNameData = model($setName)->get($setId);
    return $setNameData->name;
}











