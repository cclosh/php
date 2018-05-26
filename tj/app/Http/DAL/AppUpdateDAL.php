<?php

namespace App\Http\DAL;

use DB;

class AppUpdateDAL extends DAL
{

    protected static $table_name = 'appUpdate';

    public static function getUpdate($union)
    {
        return DB::table(static::$table_name)->where('union', $union)->orderBy('version', 'desc')->first();

    }


}
