<?php

class Fast
{
    public function run()
    {
        //自动加载
        spl_autoload_register(array($this, 'loadClass'));

        $this->saveConfig();
        $this->setReporting();
        $this->callHook();

    }

    //路由拆分
    public function callHook()
    {
        $url = $_SERVER['REQUEST_URI'];

        $index = strpos($url, '?');

        $index > 0 ? $sub_length = $index - 1 : $sub_length = strlen($url);

        $url = substr($url, 1, $sub_length);

        $url_arr = [];
        if (!empty($url)) {

            $url_arr = explode('/', $url);
        }

        $controller = isset($url_arr[0]) ? ucfirst($url_arr[0]) . 'Controller' : "IndexController";

        $function = isset($url_arr[1]) ? $url_arr[1] : "index";

        (new $controller())->$function();

    }

    //安全配置
    public function saveConfig()
    {
        ini_set('register_globals', 'Off'); //避免过来的参数变成全局变量
    }


    public function loadClass($className)
    {

        $real_path = CONTROLLER_PATH . '/' . $className . '.php';

        if (file_exists($real_path)) {
            require_once $real_path;
        }
    }

    //设置异常报警
    public function setReporting()
    {
//        error_reporting(E_ALL);  //所有异常都捕获
//        ini_set('display_errors', APP_DEBUG ? 'On' : 'Off');  //显示异常日志
//        ini_set('log_errors', 'On');   //异常日志开关
//        ini_set('error_log', RUNTIME_PATH . '/logs/error_' . date('Ymd') . '.log'); //异常日志记录文件
    }

}