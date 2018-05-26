<?php


use Workerman\Worker;

require_once 'Workerman/Autoloader.php';

//$pid = file_get_contents('_home_cclosh_SVN_cc_wm_index.php.pid');
//if ($pid > 0) {
//    system('kill -9 ' . $pid);
//}

//$path = __FILE__;
//
//$file_name = basename($path);
//
//$cmd = sprintf("ps aux|grep 'php %s' |awk '{print $2}'|xargs kill -9", $file_name);
//
//echo $cmd;
//
//exit();

// 创建一个Worker监听端口，使用http协议通讯
$http_worker = new Worker("http://0.0.0.0:10000");

// 启动4个进程对外提供服务
$http_worker->count = 4;


// 接收到浏览器发送的数据时回复hello world给浏览器
$http_worker->onMessage = function ($connection, $data) {
    // 向浏览器发送hello world
    $connection->send(json_encode($datall));
};

// 运行worker
Worker::runAll();