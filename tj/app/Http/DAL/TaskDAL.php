<?php
namespace App\Http\DAL;
use DB;

class TaskDAL{

    private static $table="answer_expert";

    /*
     * 获取清单列表
     */
    public static function getTaskList($keyword,$userID,$pageNum,$pageSize){
        return DB::table(static::$table)
            ->join('question','question.id','=','answer_expert.qID')
            ->where(function ($quest) use ($keyword){
               if(!empty($keyword)){
                   $quest->where(DB::raw('CONCAT(IFNULL(question.title,""),"||",IFNULL(question.content,""))'),'like',"%$keyword%");
               }
            })
            ->where('uID','=',$userID)
            ->select(
                'answer_expert.id as id',
                'question.id as qid',
                'answer_expert.uID',
                'question.title',
                'question.content'
            )
            ->skip(($pageNum-1)*$pageSize)
            ->take($pageSize)
            ->orderBy('answer_expert.id','desc')
            ->get();
    }

    /*
     * 获取总数
     */
    public static function getCount($keyword,$userID){
        return DB::table(static::$table)
            ->join('question','question.id','=','answer_expert.qID')
            ->where(function ($quest) use ($keyword){
                if(!empty($keyword)){
                    $quest->where(DB::raw('CONCAT(IFNULL(question.title,""),"||",IFNULL(question.content,""))'),'like',"%$keyword%");
                }
            })
            ->where('uID','=',$userID)
            ->select(
                'answer_expert.id as id',
                'question.id as qid',
                'answer_expert.uID',
                'question.title',
                'question.content'
            )
            ->count();
    }

    /*
     * 根据回复问题的专家id和分配问题的id
     */
    public static function getAnswerList($userID,$qid){
        return DB::table('answer')
            ->where(["qID"=>$qid,"answerUID"=>$userID])
            ->select('id','addTime as ndate','status','answer as acontent')
            ->first();
    }

    /*
     * 是否点赞
     */
    public static function getUserLike($userID,$aid){
        return DB::table('answerlike')->where(["aID"=>$aid,"uID"=>$userID])->count();
    }
    /*
     * 获取userID维一值
     */
    public static function getMD5UserID($userID){
      $data=DB::table('userinfo')->where('id',$userID)->select('userID')->first();
        return $data->userID;
    }



}



