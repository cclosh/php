<?php
namespace App\Http\DAL;

use DB;

class BlackSubjectDAL
{

    private static $table = "black_question";

    /*
     * 拉黑内容列表
     */
    public static function getSubjectList($keyword, $pageNum, $pageSize)
    {
        return DB::table(static::$table)
            ->join('question', 'black_question.qid', '=', 'question.id')
//            ->join('userinfo', 'question.createUID', '=', 'userinfo.id')
            ->where(function ($query) use ($keyword) {
                if(!empty($keyword)){
                    $query->where(DB::raw('CONCAT(IFNULL(question.title,""),"||",IFNULL(question.content,""))'), 'like', "%$keyword%");
                }
            })
            ->select(
                'black_question.type',
                'black_question.aid',
                'black_question.qid',
                'question.createUID as userID',
//                'userinfo.userType',
                'question.title',
                'question.content',
                'question.addTime'
//                'userinfo.backCount'
            )
            ->skip(($pageNum - 1) * $pageSize)
            ->take($pageSize)
            ->orderBy('black_question.id','desc')
            ->get();

    }

    /*
     * 获取该问题所属的内容
     */
    public static function getQidByUser($userId){
        return DB::table('userinfo')->where(["id"=>$userId])->select('id as userID','userType','backCount')->first();
    }

    /*
     * 总页数
     */
    public static function getCount(){
        return DB::table(static::$table)->count();
    }

    /*
     * 根据id获取回复用户的相关信息
     */
    public static function getIdByAnswerlist($aid)
    {
        return DB::table('answer')
            ->join('userinfo', 'userinfo.id', '=', 'answer.answerUID')
            ->join('question', 'question.id', '=', 'answer.qID')
            ->where('answer.id', '=', $aid)
            ->select(
                'userinfo.id as userID',
                'question.title',
                'userinfo.userType',
                'answer.answer as content',
                'answer.addTime',
                'userinfo.backCount'
            )
            ->first();
    }

    /*
     * 问题转入白名单
     */
    public static function getQuest_Update($qid,$type){
        DB::table('question')->where('id',$qid)->update(["isblack"=>0]);
        return DB::table('black_question')->where(["qid"=>$qid,"type"=>$type])->delete();
    }

    /*
     * 回复转入白名单
     */
    public static function getAnswer_Update($qid,$aid,$type){
        DB::table('answer')->where('id',$aid)->update(["isblack"=>0]);
        return DB::table('black_question')->where(["qid"=>$qid,"type"=>$type])->delete();
    }

}