<?php

namespace App\Http\DAL;


use App\Http\Commands\RedisHelper;
use DB;

class AnswerLikeDAL extends DAL
{

    protected static $table_name = 'answerlike';

    public static function getLikeByUserID($uID)
    {

        $db = DB::table(static::$table_name)->where('uID', $uID)->get();

        $arr = [];
        $arr['like'] = [];
        $arr['like'][$uID] = [];
        foreach ($db as $one) {

            $arr['like'][$uID][$one->aID] = $one->addTime;

        }

        return $arr;
    }


    public static function getLikeCount($aID)
    {

        return DB::table(static::$table_name)->where('aID', $aID)->count();

    }
}
