<?php

namespace App\Http\DAL;

use DB;

class NoticeDAL extends DAL
{

    protected static $table_name = 'notice';


    public static function getNotice($pageSize, $uID = 0, $maxNID = 0)
    {
        $list = DB::table('notice as n')
            ->select([
                'n.id',
                'n.questionID',
                'n.answerID',
                'n.addTime',
                'n.type',
                'n.question',
                'u.nickName',
                'u.headIcon',

            ])
            ->leftJoin('userInfo as u', 'u.id', '=', 'n.otherUID')
            ->orderBy('id', 'desc');

        $list->where('n.uID', $uID);

        if ($maxNID > 0) {
            $list->where('n.id', '<=', $maxNID);
        }


        $list = $list->paginate($pageSize);

        $list = self::pageHandel($list);

        return $list;
    }

}
