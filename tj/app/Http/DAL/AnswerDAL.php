<?php

namespace App\Http\DAL;

use DB;

class AnswerDAL extends DAL
{

    protected static $table_name = 'answer';


    public static function addAnswer($userID, $qID, $isAnonymous, $content)
    {
        $uid = UserInfoDAL::tableFirst(['userID' => $userID], ['id']);

        return DB::table(static::$table_name)->insertGetId(
            [
                'qID' => $qID,
                'answer' => $content,
                'answerUID' => $uid->id,
                'isAnonymous' => $isAnonymous,
                'addTime' => time(),
            ]
        );
    }


    public static function getUserInfo($aID)
    {
        return DB::table(static::$table_name)->select('userInfo.*')->join('userInfo', static::$table_name . '.answerUID', '=', 'userInfo.id')->where('answer.id',$aID)->first();
    }

    /*
     * 回复人id列表
     */
    public static function getAnswerList($keyword, $pageNum, $pageSize)
    {
        return DB::table(static::$table_name)
            ->join('userinfo', 'userinfo.id', '=', 'answer.answerUID')
            ->join('question', 'question.id', '=', 'answer.qID')
            ->where('answer.isblack', '=', 0)
            ->where('userinfo.userType', '=', 3)
            ->where(function ($query) use ($keyword) {
                if (!empty($keyword)) {
                    $query->where(DB::raw('CONCAT(IFNULL(userinfo.realName,""),"||",IFNULL(question.title,""),"||",IFNULL(answer.answer,""))'), 'like', "%$keyword%");
                }
            })
            ->select(
                'answer.id',
                'answer.qID',
                'question.title',
                'userinfo.id as userID',
                'userinfo.realName as userName',
                'answer.isAnonymous',
                'answer.answer as content',
                'question.view',
                'answer.addTime',
                'answer.status'
            )
            ->skip(($pageNum - 1) * $pageSize)
            ->take($pageSize)
            ->get();
    }

    /*
     * 总页数
     */
    public static function getCount($keyword)
    {
        $data = DB::table(static::$table_name)
            ->join('userinfo', 'userinfo.id', '=', 'answer.answerUID')
            ->join('question', 'question.id', '=', 'answer.qID')
            ->where('answer.isblack', '=', 0)
            ->where('userinfo.userType', '=', 3)
            ->where(function ($query) use ($keyword) {
                if (!empty($keyword)) {
                    $query->where(DB::raw('CONCAT(IFNULL(userinfo.realName,""),"||",IFNULL(question.title,""),"||",IFNULL(answer.answer,""))'), 'like', "%$keyword%");
                }
            })
            ->select('answer.id')
            ->get();
        return count($data);
    }

    /*
     * 获取点赞总数
     */
    public static function getZanCount($aid)
    {
        $data = DB::table('answerlike')->where(["aID" => $aid])->select('aid')->get();
        return count($data);
    }

    /*
     * 新增信息(测试用的)
     */
    public static function getAdd($title, $userName, $content)
    {

        $qid = DB::table('question')->insertGetId(["title" => $title, "createUID" => 18]);

        $str = ["nickName" => $userName, "realName" => $userName, "password" => 'e10adc3949ba59abbe56e057f20f883e', 'userType' => 3, 'userID' => md5(date('Y-m-d H:i:s')), 'phone' => "13" . rand(1, 100000000)];

        $uid = DB::table('userinfo')->insertGetId($str);

        DB::table('answer')->insert(['qID' => $qid, 'answerUID' => $uid, 'answer' => $content]);
        return 1;
    }


    /*
     * 根据回复id获取用户的ID
     */
    public static function getUserID($aid)
    {
        $data = DB::table('answerlike')->where('aid', $aid)->select('uID')->get();
        $arr = [];
        foreach ($data as $item) {
            $arr[] = $item->uID;
        }
        return $arr;
    }

    /*
     * 单个审核回复
     */
    public static function getfAudit($aid, $status)
    {
        return DB::table(static::$table_name)->where('id', $aid)->update(["status" => $status]);
    }

    /*
     * 批量审核回复
     */
    public static function getAuditList($aid)
    {
        return DB::table(static::$table_name)->whereIn('id', $aid)->update(["status" => 0]);
    }

    /*
     * 把回复内容拉黑
     */
    public static function getBackAnswer($id)
    {

        DB::update("update answer set isblack=1,backCount=backCount+1 WHERE id=?", [$id]);
        $data = DB::table('answer')->where('id', $id)->select('qID', 'answerUID')->first();
        DB::table('userinfo')->where('id', $data->answerUID)->update(["isblack" => 1]);  //回答用户也一起拉进黑名单
        DB::table('black_question')->insert(["qid" => $data->qID, "type" => 0, "aid" => $id]);
        return 1;
    }

}
