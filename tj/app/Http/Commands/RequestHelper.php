<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/11
 * Time: 15:38
 */

namespace App\Http\Commands;


class RequestHelper
{

    /**
     * get 请求
     * @param $url
     */
    static function Get($url)
    {
        $ch = curl_init();
        $timeout = 8;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $file_contents;
    }
}