<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // 生成应用公共文件
    '__file__' => ['common.php', 'config.php', 'database.php'],

    // 其他更多的模块定义
    'common' => [
    	'__dir__' => ['model'],
	    'model' => ['Category','Admin'],
     ],
    'api'=>[
         '__dir__'=>['controller','view'],
         'controller'=>['index','image'],
         'view'=>['index/index'],
     ],
    'admin'=>[
         '__dir'=>['controller','view'],
         'controller'=>['index'],
         'view'=>['index/index'],
    ],

];
