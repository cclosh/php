<?php
namespace App\Http\DAL;
use DB;

class RoleDAL {

    protected static $table="user_role";

    /*
     * 获取角色的列表
     */
    public static function getroleList($pageNum,$pageSize){
        return DB::table(static::$table)
            ->skip(($pageNum-1)*$pageSize)
            ->take($pageSize)
            ->get();
    }
    /*
     * 获取总数
     */
    public static function gteCount(){
        $data=DB::table(static::$table)
            ->get();
        return count($data);
    }
    /*
     * 编辑信息
     */
    public static function getAdd($model,$id){
        return DB::table(static::$table)->where('id',$id)->update($model);
    }
    /*
     * 删除
     */
    public static function getDel($id){
        return DB::table(static::$table)->where('id',$id)->delete();
    }
    /*
     * 获取所有权限
     */
    public static function getpowerList(){
        return DB::table('userpower')->select('id','title')->get();
    }



}
