<?php
namespace App\Http\DAL;
use DB;
use Illuminate\Http\Request;

class BackUserDAL{

    /*
     * 获取所有的黑名单用户
     */
    public static function getBackUserList($keyword, $pageNum, $pageSize)
    {
        return DB::table('userinfo')
            ->where('isblack', '=', 1)
            ->where(function ($query) use ($keyword) {
                if(!empty($keyword)){
                    $query->where(DB::raw('CONCAT(IFNULL(realName,""),"||",IFNULL(nickName,""),"||",IFNULL(phone,""),"||",IFNULL(email,""))'),'like',"%$keyword%");
                }
            })
            ->select(
                'id',
                'realName as userName',
                'nickName',
                'phone',
                'email',
                'userType',
                'isAnswerer',
                'registerTime as ndate',
                'backCount'
            )
            ->skip(($pageNum - 1) * $pageSize)
            ->take($pageSize)
            ->get();

    }
    /*
     * 获取总数
     */
    public static function getCount(){
        $data= DB::table('userinfo')
            ->where('isblack', '=', 1)
            ->select(
                'id'
            )
            ->get();
        return count($data);
    }

    /*
     * 黑名单转白名单用户
     */
    public static function getWhite($uid){
        return DB::table('userinfo')->where('id',$uid)->update(["isblack"=>0]);
    }

}