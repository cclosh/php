<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/3
 * Time: 13:44
 */


namespace App\Http\Commands;

use Redis;

class RedisHelper
{
    private static function getConnection()
    {
        $redis = new Redis();
        $redis->connect(config('app.redis.ip'), config('app.redis.port'));
        return $redis;
    }


    public static function set($key, $value, $second)
    {
        $redis = self::getConnection();
        if ($second > 0) {

            $redis->set($key, $value, $second);
        } else {

            $redis->set($key, $value);
        }

        $redis->close();

    }

    public static function del($key)
    {
        $redis = self::getConnection();

        $redis->del($key);

        $redis->close();

    }

    public static function get($key, $function = [], $second = 0)
    {
        $redis = self::getConnection();
        $data = $redis->get($key);

        if (!empty($function)) {

            if ($data === false) {
                $data = $function();
                if ($second > 0) {

                    $redis->set($key, serialize($data), $second);
                } else {
                    $redis->set($key, serialize($data));
                }
            } else {
                $data = unserialize($data);
            }
        }

        $redis->close();

        return $data;
        
    }
}