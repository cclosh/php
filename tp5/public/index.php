<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

function dd($data)
{
    var_dump($data);
    exit();
}

function get_defines($start_name = 'APP_PATH')
{
    $defines = get_defined_constants();
    $is_echo = false;
    foreach ($defines as $n => $v) {
        if ($n == $start_name) {
            $is_echo = true;
        }
        if ($is_echo) echo $n . ' => ' . $v . '<br/>';
    }
}

define('NEXT_LINE', '<br/>');

// [ 应用入口文件 ]
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
