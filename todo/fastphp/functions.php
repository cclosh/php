<?php

//常量设置
function setDefine($key, $value)
{
    defined($key) or define($key, $value);
}

//端点输出
function dd($arr)
{
    var_dump($arr);
    exit();
}

//加载配置
function loadConfig($arr)
{
    foreach ($arr as $key => $value) {
        setDefine($key, $value);
    }
}