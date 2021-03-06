<?php
namespace App\Http\DAL;
use DB;

class CompanyDAL{

    protected  static $table="question";

    /*
     * 企业提问
     */
    public static function getCompanyList($keyword,$pageNum,$pageSize){

        return DB::table('question')
            ->join('userinfo','userinfo.id','=','question.createUID')
            ->where(function ($query) use ($keyword){
                if(!empty($keyword)||$keyword!=""){
                    $query->where(DB::raw('CONCAT(IFNULL(userinfo.nickName,""),IFNULL(question.title,""),IFNULL(question.content,""))'),'like',"%$keyword%");
                }
            })
            ->where('question.isCompany','=',1)
            ->where('question.isblack','=',0)
            //->where('userinfo.userType','=',2)
            ->select(
                'question.id',
                'userinfo.id as userID',
                'userinfo.nickName as userName',
                'question.title',
                'question.content',
                'question.addTime',
                'question.isallot'
            )
            ->skip(($pageNum-1)*$pageSize)
            ->take($pageSize)
            ->get();

    }

    /*
     * 获取总页数
     */
    public static function getCount($keyword)
    {
        $data= DB::table('question')
            ->join('userinfo','userinfo.id','=','question.createUID')
            ->where('question.isCompany','=',1)
            ->where('question.isblack','=',0)
            //->where('userinfo.userType','=',2)
            ->where(function ($query) use ($keyword){
                if(!empty($keyword)||$keyword!=""){
                    $query->where(DB::raw('CONCAT(IFNULL(userinfo.nickName,""),IFNULL(question.title,""),IFNULL(question.content,""))'),'like',"%$keyword%");
                }
            })
            ->select(
                'question.id'
            )
            ->get();
        return count($data);
    }


    /*
     * 获取是否回复
     */
    public static function getIsAnswer($qid){
        return DB::table('answer')->where('qid',$qid)->select('id','answerUID as aID')->first();
    }

    /*
     * 回复点赞表
     */
    public static function getanswerLike($aid,$uid){
        $data= DB::table('answerlike')->where(["aID"=>$aid,"uID"=>$uid])->select('aid')->get();
        if($data){
            return 1;
        }
        return 0;
    }

    /*
     * 获取回复id
     */
    public static function getAnswer_expert($eid){
        $data=DB::table('answer_expert')->where(["qID"=>$eid])->select('uID')->first();
        if($data){
            $uid=$data->uID;
        }else{
            $uid="";
        }
        return $uid;
    }

    /*
     * 获取专家名称
     */
    public static function getExpert($uid){
        $userName="";
        $data= DB::table('userinfo')->where('id',$uid)->select('realName as userName')->first();
        if(!empty($data->userName)){
            $userName="-".$data->userName;
        }
        return $userName;
    }

    /*
     * 回复问题专家列表
     */
    public static function getAnswer_user(){
        return DB::table('userinfo')
            ->where(["userType"=>4,"isAnswerer"=>1])
            ->select('id','nickName','realName')
            ->get();
    }

    /*
     * 开始分配任务
     */
    public static function getUpdate($qid,$uid,$userName){

        $data=DB::table('answer_expert')->where(["qID"=>$qid])->first();

        if($data){
            DB::table('answer_expert')->where(["qID"=>$qid])->update(["uID"=>$uid]);
        }else{
            DB::table('answer_expert')->insert(["qID"=>$qid,"uID"=>$uid,"userName"=>$userName]);
        }
        DB::table(static::$table)->where('id',$qid)->update(['isallot'=>1]);
        return 1;
    }

    /*
     * 新增企业问题
     */
    public static function getAdd($userName, $title, $content, $ndate)
    {
        $str = ["nickName" => $userName, "realName" => $userName, 'userType' => 2, "password" => 'e10adc3949ba59abbe56e057f20f883e', 'userID' => md5(date('Y-m-d H:i:s')), 'phone' => "13254865455" . rand(1, 100000000)];
        $uid = DB::table('userinfo')->insertGetId($str);

        DB::table('question')->insert(["title" => $title, "content" => $content, "createUID" => $uid, "addTime" => strtotime($ndate)]);
        return 1;
    }

    /*
     * 专家分配
     */
    public static function getinsertExpert($model){
        return DB::table('answer_expert')->insert($model);
    }

}