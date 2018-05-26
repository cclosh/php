<?php
namespace App\Http\Commands;

class Util {
    
    private static $uploadfiles = 'uploadfiles';

    /**
     * 上传文件 
     * @param 提交的表单 $request
     * @param 上传控件的名称 $controlName
     * @param 子目录 $subPath
     * @param 自定义图片名 $customName
     * @return 返回上传后图片URL
     */
    public static function updateFile($request, $controlName, $subPath, $customName) {
        $retPath = '';
        $file = "";
        $extension = "";
        $newName = "";

        if ($request->hasFile($controlName)) {
            $file = $request->file($controlName);
            $extension = $file->getClientOriginalExtension(); //后缀
            if ($customName != "") {
                $newName = $customName . "_" . date('YmdHis') . "." . $extension;
            } else {
                $newName = date('YmdHis') . "." . $extension;
            }
            $retPath = '/' . $file->move(self::$uploadfiles . '/' . $subPath, $newName);
            $retPath = str_replace('\\', '/', $retPath);
        }

        return $retPath;
    }

    //原始图片名
    public static function UploadFile($request, $controlName, $subPath) {
        $retPath = '';
        $file = "";
        $extension = "";
        $filePath = "";

        if ($request->hasFile($controlName)) {
            $file = $request->file($controlName);
            $extension = $file->getClientOriginalName(); /* 获取原始文件名 */
            $filePath = '/' . $file->move(self::$uploadfiles . '/' . $subPath, $extension); //文件地址
            $retPath = str_replace('\\', '/', $filePath);
        }
        return $retPath;
    }
    
    /**
     * 日历皮肤包图片上传
     * @param type $id 皮肤ID，作为文件路径
     * @param type $file 上传的文件
     * @param type $subPath 上传文件路径名
     * @return type 返回上传后图片URL
     */
    public static function SkinUploadFile($id,$file, $subPath) {
        if ($file) {
            $extension = $file->getClientOriginalName(); /* 获取原始文件名 */
            $filePath = '/' . $file->move(self::$uploadfiles . '/skinpack/' .$id.'/'.$subPath,$extension); //文件地址
            $retPath = str_replace('\\', '/', $filePath);
        }
        return $retPath;
    }

    /**
     * 上传文件 
     * @param 提交的表单 $request
     * @param 上传控件的名称 $controlName
     * @param 子目录 $subPath
     * @param 自定义图片名 $customName
     * @return 返回上传后图片URL
     */
    public static function updateFileStatic($request, $controlName, $subPath) {
        $retPath = '';
        $file = "";
        $extension = "";

        if ($request->hasFile($controlName)) {
            $file = $request->file($controlName);
            $extension = $file->getClientOriginalExtension();
            $filename = $_FILES[$controlName]['name'];

            $directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $subPath . "/" . $filename; //站点根目录
            if (file_exists($directory)) {
                unlink($directory);
            }
            $retPath = '/' . $file->move(self::$uploadfiles . '/' . $subPath, $filename);
            $retPath = str_replace('\\', '/', $retPath);
        }
        return $retPath;
    }
    /**
     * 循环删除目录和文件函数 
     * @param type $dirName 需要删除的目录
     */ 
    public static function delDirAndFile( $dirName )  
    {  //echo $dirName;
        if ( $handle = opendir( "$dirName" ) ) {  
            while ( false !== ( $item = readdir( $handle ) ) ) {  
                if ( $item != "." && $item != ".." ) {  
                    if ( is_dir( "$dirName/$item" ) ) {  
                        Util::delDirAndFile( "$dirName/$item" );  
                    } else {  
                        //echo "$dirName/$item"."<br>";
                        //unlink( "$dirName/$item" );  
                    }  
                }  
            }  
            closedir( $handle );  
            //echo $dirName."<br>";
            rmdir( $dirName );  
        }  
    } 
    
    /**
     * 将文件夹打包成zip文件
     */
    public static function addFileToZip($path, $zip) {
        $handler = opendir($path); //打开当前文件夹由$path指定。
        /*
        循环的读取文件夹下的所有文件和文件夹
        其中$filename = readdir($handler)是每次循环的时候将读取的文件名赋值给$filename，
        为了不陷于死循环，所以还要让$filename !== false。
        一定要用!==，因为如果某个文件名如果叫'0'，或者某些被系统认为是代表false，用!=就会停止循环
        */
        while (($filename = readdir($handler)) !== false) {
            if ($filename != "." && $filename != "..") {  
                //文件夹文件名字为'.'和‘..’，不要对他们进行操作
                if (is_dir($path . "/" . $filename)) {// 如果读取的某个对象是文件夹，则递归
                    Util::addFileToZip($path . "/" . $filename, $zip);
                } else { //将文件加入zip对象
                    $zip->addFile($path . "/" . $filename,$filename);
                }
            }
        }
        @closedir($path);
    }
    
    /**
     * 将文件夹打包成zip文件  -- 皮肤配置包配置包
     */
    public static function addFileToZip_configure($path,$dir_name, $zip) {
        $handler = opendir($path); 
        while (($filename = readdir($handler)) !== false) {
            if ($filename != "." && $filename != "..") {  
                if(!file_exists($path . "/" . $filename)){
                    return false;
                }
                if (is_dir($path . "/" . $filename)) {
                    Util::addFileToZip($path . "/" . $filename, $zip);
                } else {
                    $zip->addFile($path . "/" . $filename,$dir_name.$filename);
                }
            }
        }
        @closedir($path);
    }
    /**
     * 复制指定的目录的所有文件到指定目录下
     * @param type $dirName 需要删除的目录
     */ 
    public static function copyFile( $dir1,$dir2 )  
    {          
        if ( $handle = opendir($dir1)) {  
            while ( false !== ( $item = readdir( $handle ) ) ) {  
                if ( $item != "." && $item != ".." ) { 
                    if ( is_dir( "$dir1/$item" ) ) {  echo "$dir1/$item";exit;
                        Util::delDirAndFile( "$dirName/$item" );  
                    } else {  
                        copy("$dir1/$item","$dir2/$item");  
                    }  
                }  
            }  
            closedir( $handle );   
        }  
    } 
    
    
    /**
     * 循环读取文件
     * @param type $dirName 需要查找的目录
     */ 
    public static function getFile( $dirName )  
    {  
        if ( $handle = opendir( "$dirName" ) ) {  
            while ( false !== ( $item = readdir( $handle ) ) ) {  
                if ( $item != "." && $item != ".." ) {  
                    if ( is_dir( "$dirName/$item" ) ) {  
                        Util::getFile( "$dirName/$item" );  
                    } else {  
                       $file[] = "$dirName/$item";  
                        //echo "$dirName/$item";
                    }  
                }  
            }  
            //closedir( $handle );  
            //rmdir( $dirName );  
        }  
        return $file;
    }
    
    public static function Get_user_info(){
        $is_logged = self::Get_josn_user('get_is_logged');
        $user_id = self::Get_josn_user('get_user_id');
        $user_info = self::Get_josn_user('get_user_info');
        if($is_logged['result'] == 1 && $is_logged['result'] == 1 && $is_logged['result'] == 1){
            return $user_info['info'];
        }  else {
            self::Get_user_info('get_is_logged');
        }        
    }
    
    public static function Get_josn_user($type){
        $result = array();
        $appkey = '10000002'; //string 应用id
        $appsecret = '55849ddf590919feb7cea751d604be99';//string 应用密匙
        //判断时间值是否存在
        if (isset($_COOKIE['inside_ticket_time'])) {
            $ticket_time = $_COOKIE['inside_ticket_time'];
            //判断登录时间是否超时
            if($ticket_time >= time()){
                //验证登录状态参数               
                
                $ticket_key_id = $_COOKIE['inside_ticket_key_id'];
                $ticket_id = $_COOKIE['inside_ticket_id'];
                
                $field = $ticket_key_id.','.$ticket_id; //string 请求参数
                
                $sign = md5($appkey.$appsecret.$type.$field.'dtlpassport2016'); //string 加密

                $result = self::Get_post_curl($type,['appkey' => $appkey,'appsecret' => $appsecret,'field' => $field,'sign' => $sign]);
                
                if($result['result'] == 1){
                    return $result;
                }     
            }
        }

        Header("Location:http://passport.updrv.com/login?back_url=http://ver.160.com/api/set_cookie&back_path=/");
        exit;
    }
    
    
    public static function Get_post_curl($act,$data){
        
        $url =  'http://passport.updrv.com/api/passport/'.$act;
        
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//$data ARRAY类型字符串
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); //相当关键，这句话是让curl_exec($ch)返回的结果可以进行赋值给其他的变量进行，json的数据操作，如果没有这句话，则curl返回的数据不可以进行人为的去操作（如json_decode等格式操作）
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        $result = curl_exec($ch);
        
        if($result){
            return json_decode($result,TRUE);
        }else{
            return FALSE;
        }
    }
    
    
    
    public static function Https_Post_Curl_Xcx($url, $data = array(), $timeout = 30, $CA = true){  
        $cacert = getcwd() . '/ca/cacert.pem'; //CA根证书  
        $SSL = substr($url, 0, 8) == "https://" ? true : false;  

        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout-2);  
        if ($SSL && $CA) {  
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   // 只信任CA颁布的证书  
            curl_setopt($ch, CURLOPT_CAINFO, $cacert); // CA根证书（用来验证的网站证书是否是CA颁布）  
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名，并且是否与提供的主机名匹配  
        } else if ($SSL && !$CA) {  
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书  
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名  
        }  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //避免data数据过长问题  
        curl_setopt($ch, CURLOPT_POST, true);  
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
        //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //data with URLEncode  

        $ret = curl_exec($ch);  
        //var_dump(curl_error($ch));  //查看报错信息  

        curl_close($ch);  
        return $ret;    
    }  
    
    
}
