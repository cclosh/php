<?php
namespace App\Http\DAL;
use DB;

class PquestionDAL{

    /*
     * 问题列表
     */

    private static $table="question";

    /*
     * 问题列表
     */
    public static function getList($pageNum,$pageSize,$keyword,$status){
        return DB::table(static::$table)
            ->join('userinfo','userinfo.id','=','question.createUID')
            ->where('question.isblack','=',0)
            ->where('question.isCompany','=',0)
            ->where(function ($query) use($keyword,$status){
                if(!empty($keyword)){
                    $query->where(DB::raw('CONCAT(IFNULL(userinfo.realName,""),"||",IFNULL(question.title,""),"||",IFNULL(question.content,""))'),'like',"%$keyword%");
                }
                if($status==1){
                    $query->where('question.status','=',1);
                }elseif($status==2){
                    $query->where('question.status','=',0);
                }else{

                }
            })
            ->skip(($pageNum-1)*$pageSize)
            ->take($pageSize)
            ->select(
                'question.id',
                'userinfo.id as userID',
                'userinfo.realName as userName',
                'question.title',
                'question.content',
                'question.view',
                'question.follow',
                'question.answer',
                'question.status',
                'question.isAnonymous',
                'question.addTime'
            )
            ->orderBy('question.id','desc')
            ->get();
    }

    /*
     * 获取总数
     */
    public static function getCount($keyword,$status){
        $data= DB::table(static::$table)
            ->join('userinfo','userinfo.id','=','question.createUID')
            ->where('question.isCompany','=',0)
            ->where(function ($query) use($keyword,$status){
                if(!empty($keyword)){
                    $query->where(DB::raw('CONCAT(IFNULL(userinfo.realName,""),"||",IFNULL(question.title,""),"||",IFNULL(question.content,""))'),'like',"%$keyword%");
                }
                if($status==1){
                    $query->where('question.status','=',1);
                }elseif($status==2){
                    $query->where('question.status','=',0);
                }else{
                }
            })
            ->select('question.id')
            ->get();
        return count($data);
    }

    /*
     * 获取该问题的回复用户id
     */
    public static function getAnswer($qid){
        $data= DB::table('answer')->where('qID',$qid)->select('answerUID as userID')->get();
        $arr=[];
        foreach ($data as $item){
            $arr[]=$item->userID;
        }
        return $arr;
    }

    /*
     * 用户关注问题的id
     */
    public static function getUser_question($qid){
        $data= DB::table('user_question')->where('qID',$qid)->select('uID as UserID')->get();
        $arr=[];
        foreach ($data as $item){
            $arr[]=$item->UserID;
        }
        return $arr;
    }

    /*
     * 新增用户
     */
    public static function AddUser($userName){
        $str=["nickName"=>$userName,"realName"=>$userName,"password"=>'e10adc3949ba59abbe56e057f20f883e',"userType"=>1,'userID'=>md5(date('Y-m-d H:i:s')),'phone'=>"13".rand(1,100000000)];
        return DB::table('userinfo')->insertGetId($str);
    }
    /*
     * 新增问题
     */
    public static function getAdd($model){
        return DB::table(static::$table)->insert($model);
    }
    /*
     * 审核状态
     */
    public static function getStatus($qid,$status){
        return DB::table(static::$table)->where('id',$qid)->update(["status"=>$status]);
    }

    /*
     * 批量审核
     */
    public static function getStausList($qid){
        return DB::table(static::$table)->whereIn('id',$qid)->update(["status"=>0]);
    }

    /*
     * 黑名单（单个）
     */
    public static function getBack($qid)
    {
        //DB::table('black_question')->insert(["qid" => $qid]);
        $data = DB::table('question')->where('id', $qid)->select('createUID')->first();
        DB::update("update userinfo set isblack=1 , backCount=backCount+1 where id=?", [$data->createUID]);
        DB::update("update question set isblack=1 , backCount=backCount+1 where id=?", [$qid]);
        DB::table('black_question')->insert(["qid"=>$qid,"type"=>1]);
        return 1;
    }

}
