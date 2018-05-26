<?php

namespace App\Http\DAL;


use App\Http\Commands\RedisHelper;
use DB;

class UserInfoDAL extends DAL
{

    protected static $table_name = 'userInfo';

    public static function getUserUID($userID)
    {
        return RedisHelper::get('answer_userID_' . $userID, function () use ($userID) {
            $userInfo = DB::table(static::$table_name)->where('userID', $userID)->first();

            if ($userInfo) {
                return $userInfo->id;
            } else {
                return 0;
            }
        }, 60 * 60 * 24);


    }


}
