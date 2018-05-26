<?php

namespace App\Http\Commands;

class SendSMS
{

    private static $Uid = 'ucenter';
    private static $Key = '91a07112f5c92bbcf6f3';

    public static function PostSMS($smsMob, $smsText)
    {


        $smsText = urlencode($smsText);

        $url = 'http://utf8.sms.webchinese.cn/?Uid=' . self::$Uid . '&Key=' . self::$Key . '&smsMob=' . $smsMob . '&smsText=' . $smsText . "【人生日历】";
 
        if (function_exists('file_get_contents')) {
            $file_contents = file_get_contents($url);
        } else {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            curl_close($ch);
        }


        return $file_contents;
    }

}
