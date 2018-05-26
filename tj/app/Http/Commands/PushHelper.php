<?php
/**
 * Created by PhpStorm.
 * User: cclosh
 * Date: 18-3-1
 * Time: 下午2:13
 */

namespace App\Http\Commands;

require('../app/Http/Commands/xinge-api-php/src/XingeApp.php');

class PushHelper
{

    protected static $ACCESS_ID_Android = 2100277408;
    protected static $SECRET_KEY_Android = "34fd863dc9c568fbd887721080a76a6f";

    protected static $ACCESS_ID_IOS = 2200277581;
    protected static $SECRET_KEY_IOS = "ead500179e2d2c15bcca33f07e4f92ad";


    public static function pushToToken($title, $content, $token)
    {
        $tokenLen = strlen($token);

        if ($tokenLen == 64) {
            return \XingeApp::PushTokenIos(self::$ACCESS_ID_IOS, self::$SECRET_KEY_IOS, $content, $token, \XingeApp::IOSENV_DEV);

        } elseif ($tokenLen == 40) {
            return \XingeApp::PushTokenAndroid(self::$ACCESS_ID_Android, self::$SECRET_KEY_Android, $title, $content, $token);
        } else {
            return false;
        }
    }
}