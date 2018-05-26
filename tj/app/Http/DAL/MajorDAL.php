<?php
namespace App\Http\DAL;
use DB;
class MajorDAL{

    /*
     * 获取专业答主的列表
     */
    public static function getMajorList($keyword,$pageNum,$pageSize){

        return DB::table('userinfo')
            ->join('answer','answer.answerUID','=','userinfo.id')
            ->join('question','question.id','=','answer.qID')
            ->where('userinfo.userType','=',4)  //专业答主标识
            ->where(function ($query) use ($keyword){
                if(!empty($keyword)){
                    $query->where(DB::raw('CONCAT(IFNULL(userinfo.nickName,""),"||",IFNULL(question.title,""),"||",IFNULL(answer.answer,""))'),'like',"%$keyword%");
                }
            })
            ->select(
                'answer.id',
                'userinfo.id as userID',
                'userinfo.nickName as userName',
                'question.createUID',
                'answer.qID',
                'question.title',
                'answer.answer as content',
                'answer.addTime',
                'answer.status'
            )
            ->skip(($pageNum-1)*$pageSize)
            ->take($pageSize)
            ->get();
    }

    /*
     * 获取总页数
     */
    public static function getCount($keyword){
        $data= DB::table('userinfo')
            ->join('answer','answer.answerUID','=','userinfo.id')
            ->join('question','question.id','=','answer.qID')
            ->where('userinfo.userType','=',4)  //专业答主标识
            ->where(function ($query) use ($keyword){
                if(!empty($keyword)){
                    $query->where(DB::raw('CONCAT(IFNULL(userinfo.nickName,""),"||",IFNULL(question.title,""),"||",IFNULL(answer.answer,""))'),'like',"%$keyword%");
                }
            })
            ->select(
                'answer.id'
            )
            ->get();
        return count($data);
    }


    /*
     * 批量删除
     */
    public static function getDel($id){
        return DB::table('answer')->whereIn('id',$id)->delete();
    }


    /*
     * 是否点赞
     */
    public static function getlike($aid){
        $data= DB::table('answerlike')
            ->where('aID',$aid)
            ->select('aID')
            ->get();
        if($data){
            return 1;
        }
        return 0;
    }

    /*
     * 新增专业答题
     */
    public static function getAdd($userName,$title,$content,$ndate){

        $str=["nickName"=>$userName,"realName"=>$userName,"password"=>'e10adc3949ba59abbe56e057f20f883e','userType'=>4,'isAnswerer'=>1,'userID'=>md5(date('Y-m-d H:i:s')),'phone'=>"13".rand(1,100000000)];

        $uid=DB::table('userinfo')->insertGetId($str);

        $qid=DB::table('question')->insertGetId(["title"=>$title,"createUID"=>30,"isAnonymous"=>1,"addTime"=>$ndate]);

         DB::table('answer')->insert(["qID"=>$qid,"answerUID"=>$uid,"answer"=>$content]);
         return 1;
    }

    /*
     * 回复拉入黑名单
     */
    public static function getAddBlack($qid,$aid){
        DB::table('black_question')->insert(["qid"=>$qid,"aid"=>$aid,"type"=>0]);
        return DB::update("update answer set isblack=1,backCount=backCount+1 WHERE id=?",[$aid]);
    }
    /*
     * 审核回复
     */
    public static function getMAudit($aid,$status){
      return DB::table('answer')->where('id',$aid)->update(["status"=>$status]);
    }

}


