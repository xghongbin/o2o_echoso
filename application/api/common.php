<?php
/**
 * Created by PhpStorm.
 * User: xghongbin
 * Date: 2017/4/1
 * Time: 0:27
 */

/*
 * 将数据返回给JS
 * 参数1：返回的判断状态：1，成功，0，错误
 * 参数2：返回的信息
 * 参数3：返回的数据：数组形式，默认为空
 * */
function show($status,$message='',$data=[]){
    return [
        'status'=>intval($status),
        'message'=>$message,
        'data'=>$data,
    ];
}