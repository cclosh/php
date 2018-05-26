<?php


namespace App\Http\Lib;

set_time_limit(0);

class WebAutoUpdate
{
    protected static $receiveFile = '/data/receiveFile';  //统计接收文件目录
    protected static $sch_helper = "/data1/web/itcm.updrv.com/SchHelper.py";  //scp文件上传脚本

    /**
     * @param $web_infos 数组，格式如下
     *                      [[
     * 'ip' => '192.168.100.202',
     * 'username' => 'root',
     * 'password' => 'xxx',
     * 'update_path' => '/data/code',
     * ]]
     * @param $file_path  要更新zip包
     * @param $id         it变更id
     * @return array
     * @throws \Exception
     */
    public static function autoUpdate($web_infos, $file_path, $id)
    {
        $ip_ret = [];
        foreach ($web_infos as $web_info) {

            $ip = $web_info['ip'];
            $username = $web_info['username'];
            $password = $web_info['password'];
            $new_path = self::$receiveFile . '/' . $id . '.zip';
            $update_path = $web_info['update_path'];

            $sch_helper = self::$sch_helper;

            $cmd = sprintf("python3 %s %s %s %s %s %s %s", $sch_helper, $ip, 22, $username, $password, $file_path, $new_path);

//            $ret = exec($cmd);
//
//            if ($ret != "True") {
//
//                throw new \Exception("scp($ip)传输错误：" . $ret);
//            }

            $parm['cmd'] = 1;
            $parm['fileName'] = $new_path;
            $parm['updatePath'] = $update_path;

            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create  socket\n");

//            socket_set_option($socket,SOL_SOCKET,SO_RCVTIMEO,array("sec"=>2, "usec"=>0 ) );

            $connection = socket_connect($socket, $ip, "6671");
            socket_write($socket, json_encode($parm));

            while ($out = socket_read($socket, 2048)) {
                $ip_ret[$ip] = json_decode($out, 1);
            }

            socket_close($socket);
        }

        return $ip_ret;

    }

    /**
     * 文件还原
     * @param $web_infos  数组，格式如下
     *                      [[
     * 'ip' => '192.168.100.202',
     * 'update_path' => '/data/code',
     * ]]
     * @param $id   it变更的id
     * @return array
     */
    public static function reload($web_infos, $id)
    {
        $ip_ret = [];
        foreach ($web_infos as $web_info) {

            $ip = $web_info['ip'];
            $update_path = $web_info['update_path'];

            $parm['cmd'] = 3;
            $parm['id'] = (string)$id;
            $parm['updatePath'] = $update_path;

            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create  socket\n");

            socket_connect($socket, $ip, "6671");
            socket_write($socket, json_encode($parm));

            while ($out = socket_read($socket, 2048)) {
                $ip_ret[$ip] = json_decode($out, 1);
            }

            socket_close($socket);
        }

        return $ip_ret;

    }

}

/*使用案例
WebAutoUpdate::autoUpdate([
    [
        'ip' => '192.168.100.202',
        'username' => 'root',
        'password' => '123456!',
        'update_path' => '/data/code',
    ],
    [
        'ip' => '192.168.100.99',
        'username' => 'root',
        'password' => '123456!',
        'update_path' => '/data/code',
    ],

], '/home/cclosh/Desktop/xc.160.com.zip', 10001);

WebAutoUpdate::reload([
    [
        'ip' => '192.168.100.202',
        'update_path' => '/data/code',
    ],
    [
        'ip' => '192.168.100.99',
        'update_path' => '/data/code',
    ],

], 10001);