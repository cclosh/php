<?php

namespace App\Http\DAL;

use DB;

class QuestionDAL extends DAL
{

    protected static $table_name = 'question';

    public static function getQuestionBase($ids = [], $pageNum, $pageSize, $uID = 0, $isCompany = -1, $keyword = "", $maxQID = 0, $fUID = 0)
    {
        $list = DB::table('question as q')
            ->select(DB::raw(
                'q.id,
                u.nickName,
                u.headIcon,
                q.title,
                q.content,
                q.follow,
                q.view,
                q.isAnonymous,
                q.isCompany,
                q.answer,
                q.addTime,
                0 as isFromMySelf,
                q.createUID'
            ))
            ->leftJoin('userInfo as u', 'u.id', '=', 'q.createUID')
            ->where('u.forbidden', '0')
            ->where('q.isblack', 0)
            ->where('q.status', 1)
            ->orderBy('addTime', 'desc');

        if ($ids) {
            $list->whereIn('q.id', $ids);
        }

        if ($fUID > 0) {
            $list->where('q.createUID', '=', $fUID);
        }

        if ($maxQID > 0) {
            $list->where('q.id', '<=', $maxQID);
        }

        if ($isCompany >= 0) {
            $list->where('q.isCompany', $isCompany);
        }

        if (!empty($keyword)) {
            KeywordDAL::tableDelete([
                'uID' => $uID,
                'keyword' => $keyword
            ]);

            KeywordDAL::tableInsert([
                'uID' => $uID,
                'keyword' => $keyword,
                'addTime' => time()
            ]);
            $list->where(function ($q) use ($keyword) {
                $q->where('q.title', 'like', "%$keyword%")->orWhere('q.content', 'like', "%$keyword%");
            });
        }

        $list = $list->paginate($pageSize);

        $list = self::pageHandel($list);


        if ($list->data) {

            $likeAndFollow = User_questionDAL::getFollowByUserID($uID);
            foreach ($list->data as $one) {
                if ($one->isAnonymous) $one->nickName = "匿名用户";
                $one->isFromMySelf = $one->createUID == $uID ? 1 : 0;
                $one->isFollow = isset($likeAndFollow['follow'][$uID][$one->id]) ? 1 : 0;
                unset($one->createUID);
            }
        }

        return $list;
    }


    public static function getCompanyQuestion($pageNum, $pageSize, $uID = 0, $maxQID = 0)
    {

        $data = self::getQuestionBase([], $pageNum, $pageSize, $uID, 1, "", $maxQID, $uID);

        $qIDs = [];
        foreach ($data->data as $one) {
            $qIDs[] = $one->id;
        }

        //获取回复
        $answer = DB::table('answer as a')
            ->select(DB::raw("
                a.qid,
                a.id, 
                a.view,
                u.nickName,
                u.headIcon,
                a.answer,
                a.answerUID,
                0 as isFromMySelf,
                a.addTime,
                a.answerUID,
                a.isAnonymous,
                0 as isLike,
                (select count(0) from answerLike where aID=a.id) as `like`"
            ))
            ->leftJoin('userInfo as u', 'u.id', '=', 'a.answerUID')
            ->where('u.forbidden', '0')
            ->whereIn('a.qid', $qIDs)
            ->where('a.isblack', 0)
            ->orderBy('a.qid')
            ->orderBy('a.id', 'desc')
            ->get();

        $answerArr = [];


        $userAnswerLike = AnswerLikeDAL::getLikeByUserID($uID);


        foreach ($answer as $one) {

            if ($one->isAnonymous) $one->nickName = "匿名用户";

            if (isset($userAnswerLike['like'][$uID][$one->id])) {
                $one->isLike = 1;
            }
            $answerArr[$one->qid][] = $one;
        }

        foreach ($data->data as $one) {
            if (isset($answerArr[$one->id])) {
                $one->answer = $answerArr[$one->id];
            } else {
                $one->answer = [];
            }
        }


        return $data;
    }

    public static function getUserInfo($qID)
    {
        return DB::table(static::$table_name)->select(DB::raw('userInfo.*,question.isCompany,question.title'))->join('userInfo', static::$table_name . '.createUID', '=', 'userInfo.id')->where('question.id', $qID)->first();
    }

    public static function questionAddView($qID)
    {
        return DB::table(static::$table_name)->where('id', $qID)->increment('view', 1);

    }

    public static function questionAddFllow($qID, $add = 1)
    {
        return DB::table(static::$table_name)->where('id', $qID)->increment('follow', $add);
    }

    public static function getQuestionBaseByQID($qID, $uID = 0, $pageNum, $pageSize, $maxAID = 0, $aID = 0)
    {
        $question = DB::table('question as q')
            ->select(DB::raw(
                'q.id,
                u.nickName,
                u.headIcon,
                q.title,
                q.follow,
                q.content,
                q.view,
                q.isAnonymous,
                q.isCompany,
                q.answer,
                q.addTime,
                0 as isFromMySelf,
                q.createUID'
            ))
            ->leftJoin('userInfo as u', 'u.id', '=', 'q.createUID')
            ->where('u.forbidden', '0')
            ->where('q.id', $qID)
            ->where('q.status', 1)
            ->first();

        if ($question) {


            if ($question->isAnonymous) $question->nickName = "匿名用户";

            $likeAndFollow = User_questionDAL::getFollowByUserID($uID);

            $question->isFollow = isset($likeAndFollow['follow'][$uID][$question->id]) ? 1 : 0;

            $question->isFromMySelf = $question->createUID == $uID ? 1 : 0;
            unset($question->createUID);
        }

        $aIDs = [];//获取aid,然后加1

        //获取回复
        $answer = DB::table('answer as a')
            ->select(DB::raw("
                a.id, 
                a.view,
                u.nickName,
                u.headIcon,
                a.answer,
                a.answerUID,
                0 as isFromMySelf,
                a.addTime,
                a.answerUID,
                a.isAnonymous,
                0 as  isLike,
                (select count(0) from answerLike where aID=a.id) as `like`"
            ))
            ->leftJoin('userInfo as u', 'u.id', '=', 'a.answerUID')
            ->where('u.forbidden', '0')
            ->where('a.qid', $qID)
            ->where('a.isblack', 0)
            ->orderBy('addTime', 'desc');

        if ($maxAID > 0) {
            $answer->where('a.id', '<=', $maxAID);
        }

        if ($aID > 0) {
            $answer->where('a.id', '=', $aID);
        }

        $answer = $answer->paginate($pageSize);

        $answer = self::pageHandel($answer);


        $userAnswerLike = AnswerLikeDAL::getLikeByUserID($uID);


        //处理回复的view
        foreach ($answer->data as $one) {


            if ($one->isAnonymous) $one->nickName = "匿名用户";

            $aIDs[] = $one->id;
            $one->view += 1;

            $one->isFromMySelf = $one->answerUID == $uID ? 1 : 0;
            unset($one->answerUID);

            if (isset($userAnswerLike['like'][$uID][$one->id])) {
                $one->isLike = 1;
            }

        }
        //view加1
        if ($aIDs) {
            DB::table('answer')->whereIn('id', $aIDs)->increment('view', 1);
        }

        $like = AnswerLikeDAL::getLikeByUserID($uID);

        foreach ($answer->data as $one) {

            $one->isLike = isset($like['like'][$uID][$one->id]) ? 1 : 0;
        }

        return $list = [
            'question' => $question,
            'answer' => $answer,
        ];
    }

    public static function getUserFollowQuestionList($userID, $pageNum, $pageSize, $maxQID)
    {
        $uid = UserInfoDAL::getUserUID($userID);

        $ids_DB = User_questionDAL::getFollowByUserID($uid);

        $ids = [];

        $list = [];

        if (isset($ids_DB['follow'][$uid])) {

            foreach ($ids_DB['follow'][$uid] as $key => $value) $ids[] = $key;

            $ids = empty($ids) ? [-1] : $ids; //j如果为空则查-1的记录

            $list = self::getQuestionBase($ids, $pageNum, $pageSize, $uid, -1, "", $maxQID);
        }

        return $list;
    }

    public static function getUserQuestionList($userID, $pageNum, $pageSize, $maxQID)
    {
        $uid = UserInfoDAL::getUserUID($userID);

        $ids_DB = self::tableGet(['createUID' => $uid]);

        $ids = [];

        $list = [];

        if ($ids_DB) {

            foreach ($ids_DB as $one) $ids[] = $one->id;

            $list = self::getQuestionBase($ids, $pageNum, $pageSize, $uid, -1, "", $maxQID);
        }

        return $list;
    }


    public static function getQuestionIndex($userID, $pageNum, $pageSize, $keyword, $maxQID)
    {
        $uid = UserInfoDAL::getUserUID($userID);


        $list = self::getQuestionBase([], $pageNum, $pageSize, $uid, 0, $keyword, $maxQID);


        return $list;
    }

    public static function getUserAnswerList($userID, $pageNum, $pageSize, $maxAID)
    {
        $uid = UserInfoDAL::getUserUID($userID);

        $ids_DB = AnswerDAL::tableGet(['answerUID' => $uid]);

        $ids = [];

        $list = [];

        if ($ids_DB) {

            foreach ($ids_DB as $one) $ids[] = $one->qID;

            $list = self::getQuestionBase($ids, $pageNum, $pageSize, $uid, -1, "", $maxAID);
        }

        return $list;
    }

    public static function getMyReflection($userID)
    {
        $uid = UserInfoDAL::getUserUID($userID);

        $list = DB::select('
                                select (SELECT COUNT(0) from question where createUID=? and isblack=0) as questionCount,
                                (SELECT  count(0)   from answer  where answerUID=? and isblack=0) as answerCount,
                                (SELECT  count(0) from user_question where uID=? ) as followCount,
                                (SELECT count(0)  from answerlike where aID in (SELECT id from answer  where answerUID=?)) as achievementCount', [$uid, $uid, $uid, $uid]);

        return $list;
    }


    public static function getMyAchievement($userID, $pageNum, $pageSize, $maxAID)
    {
        $uid = UserInfoDAL::getUserUID($userID);

        $list = DB::table('answer as a')
            ->select(DB::raw('q.id as qID,q.title,a.addTime,al.addTime as likeTime,al.realName,al.headIcon'))
            ->leftJoin(DB::raw('(SELECT * from answerlike LEFT JOIN userinfo on answerlike.uID=userinfo.id) as al'), 'a.id', '=', 'al.aID')
            ->leftJoin('question as q', 'a.qID', '=', 'q.id')
            ->orderBy('al.addTime', 'desc')
            ->where('a.answerUID', $uid)
            ->where('a.isblack', 0)
            ->where('q.isblack', 0);

        if ($maxAID > 0) {
            $list->where('a.id', '<=', $maxAID);
        }
        $list = $list->paginate($pageSize);

        $list = self::pageHandel($list);

        return $list;
    }


}
