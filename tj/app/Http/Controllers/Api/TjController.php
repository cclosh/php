<?php

namespace App\Http\Controllers\Api;

use App\Http\Commands\RedisHelper;
use App\Http\Controllers\ApiBaseController;
use App\Http\Lib\WebAutoUpdate;
use Illuminate\Http\Request;

class TjController extends ApiBaseController
{

    protected $tj_key = "tj";

    public function tj(Request $request)
    {
        header("Access-Control-Allow-Origin:*");


        $key = $request->get('key', $this->tj_key);

        $count = RedisHelper::get($key);

        if ($count === false) {
            $count = 0;
        }

        $count = intval($count) + 1;

        RedisHelper::set($key, $count, 3600 * 24);


        //echo 'tj(1)';


    }

    public function count(Request $request)
    {
        $key = $request->get('key', $this->tj_key);

        return RedisHelper::get($key);
    }


    public function auto()
    {
        WebAutoUpdate::autoUpdate([
//            [
//                'ip' => '192.168.100.202',
//                'username' => 'root',
//                'password' => '123456!',
//                'update_path' => '/data/code',
//            ],
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
    }

}
