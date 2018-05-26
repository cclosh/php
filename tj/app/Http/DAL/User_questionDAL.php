<?php

namespace App\Http\DAL;

use App\Http\Commands\RedisHelper;
use DB;

class User_questionDAL extends DAL
{

    protected static $table_name = 'user_question';

    public static function getFollowByUserID($uID)
    {
        $db = DB::table(static::$table_name)->where('uID', $uID)->get();

        $arr = [];
        $arr['follow'] = [];
        $arr['follow'][$uID] = [];
        foreach ($db as $one) {

            $arr['follow'][$uID][$one->qID] = $one->addTime;

        }

        return $arr;

    }

}
