<?php
namespace App\Http\DAL;
use DB;

class PowerDAL{

    protected  static $table="userpower";

    /*
     * 获取所有的权限列表
     */
    public static function getInfo($pageNum,$pageSize){
        return DB::table(static::$table)
            ->skip(($pageNum-1)*$pageSize)
            ->take($pageSize)
            ->get();
    }

    /*
     * 总条数
     */
    public static function getCount(){
        $data= DB::table(static::$table)
            ->get();
        return count($data);
    }
    /*
     * 新增/编辑数据
     */
    public static function getAdd($model,$id){
       if($id>0){
           return DB::table(static::$table)->where('id',$id)->update($model);
       }else{
           return DB::table(static::$table)->insert($model);
       }
    }

    /*
     * 删除信息
     */
    public static function getDel($id){
        return DB::table(static::$table)->where('id',$id)->delete();
    }
    /*
     * 批量删除
     */
    public static function getDelList($id){
        return DB::table(static::$table)->whereIn('id',$id)->delete();
    }

}






