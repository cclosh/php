<?php

namespace App\Http\DAL;

use DB;

class KeywordDAL extends DAL
{

    protected static $table_name = 'keyword';

    public static function getKeyword($userID, $pageSize)
    {
        $uid = UserInfoDAL::tableFirst(['userID' => $userID], ['id']);

        $list = DB::table(static::$table_name)->where('uID', $uid->id)->orderBy('id', 'desc')->paginate($pageSize);

        return self::pageHandel($list);

    }


    public static function deleteKeywordBykID($userID, $kID)
    {
        self::tableDelete([
            'id' => $kID,
        ]);

        return true;

    }


}
