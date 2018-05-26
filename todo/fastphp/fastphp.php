<?php

//定义必要配置项
define('ROOT', __DIR__);

//加载框架方法
require ROOT . '/functions.php';

setDefine('CONFIG_PATH', APP_PATH . '/config');
setDefine('RUNTIME_PATH', APP_PATH . '/runtime');
setDefine('CONTROLLER_PATH', APP_PATH . '/application/controller');

setDefine('APP_DEBUG', false);

const EXT = '.class.php';

//加载配置信息
loadConfig(require CONFIG_PATH . '/config.php');

//加载框架类
require ROOT . '/core.php';

//框架启动
(new Fast())->run();




