<?php
namespace App\Http\DAL;
use DB;

class UserListDAL{

    protected static $table="userinfo";  //表名

    /*
     * 获取所有的用户信息
     * $keyword  关键是
     * $row 行数
     */
    public static function getUserList($keyword,$status,$pageNum,$pageSize)
    { 

        return DB::table(static::$table)
            ->where(function ($query) use ($keyword,$status){
                if (!empty($keyword)) {
                    $query->where(
                        DB::raw('CONCAT(IFNULL(realName,""),"||",IFNULL(nickName,""),"||",IFNULL(phone,""),"||",IFNULL(email,""))'),'like',"%$keyword%"
                    );
                }
                if ($status != 0) {
                    if($status==1){
                        $query->where('userType','=',2);
                    }elseif ($status==2){
                        $query->where('userType','=',1);
                    }elseif ($status==3){
                        $query->where('forbidden','=',1);
                    }else{
                        $query->where('forbidden','=',0);
                    }
                }
            })
            ->where('isblack','=',0)
            ->select('id',
                'realName as userName',
                'nickName',
                'phone',
                'email',
                'userType',
                'isAnswerer',
                'company',
                'job',
                'isAudit',
                'forbidden',
                'registerTime',
                'remark',
                'content',
                'details')
            ->skip($pageNum)
            ->take($pageSize)
            ->orderBy('id','desc')
            ->get();
    }
    /*
     * 总数
     */
    public static function getCount($keyword,$status)
    {
        $data= DB::table(static::$table)
            ->where(function ($query) use ($keyword,$status){
                if (!empty($keyword)) {
                    $query->where(
                        DB::raw('CONCAT(IFNULL(realName,""),"||",IFNULL(nickName,""),"||",IFNULL(phone,""),"||",IFNULL(email,""))'),'like',"%$keyword%"
                    );
                }
                if ($status != 0) {
                    if($status==1){
                        $query->where('userType','=',2);
                    }elseif ($status==2){
                        $query->where('userType','=',1);
                    }elseif ($status==3){
                        $query->where('forbidden','=',1);
                    }else{
                        $query->where('forbidden','=',0);
                    }
                }
            })
            ->select('id')
            ->get();
        return count($data);
    }

    /*
     * 效验验证码是否重复
     */
    public static function getDup($phone){
        return DB::table(static::$table)->where(["phone"=>$phone])->count();
    }
    /*
     * 新增/更新用户信息
     */
    public static function getInsert($model,$id){
        if($id>0){
            return DB::table(static::$table)->where('id',$id)->update($model);
        }else{
            return DB::table(static::$table)->insert($model);
        }
    }

    /*
     * 删除单挑记录
     */
    public static function getDel($id){
        return DB::table(static::$table)->where('id',$id)->delete();
    }
    /*
     * 批量删除
     */
    public static  function getAllDel($id){
        return DB::table(static::$table)->whereIn('id',$id)->delete();
    }

    /*
     * 答主审核
     */
    public static function getAudit($id,$shenhe,$isAnswerer=0){
        if($shenhe==1){
            $isAnswerer=1;
        }
      return DB::table(static::$table)->where('id',$id)->update(["isAnswerer"=>$isAnswerer,"isAudit"=>$shenhe]);
    }


}




