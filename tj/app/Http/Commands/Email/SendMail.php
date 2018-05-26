<?php

namespace App\Http\Commands\Email;
require_once('class.phpmailer.php');
require_once('class.smtp.php');

class SendMail
{
    //发送邮件的方法
    //$address  邮件地址
    //$title 邮件标题
    //$content 邮件内容

    private static $MAILHOST = 'smtp.160.com';
    private static $MAILPORT = 25;
    private static $MAILUSERNAME = 'notice.updrv@160.com';
    private static $MAILPASS = 'ugqvwdu8';
    private static $MAILFROM = 'notice.updrv@160.com';
    private static $MAILDISPLAYNAME = '';

    public static function postmail($address, $title, $content)
    {
        $MAILDISPLAYNAME = $address;
        error_reporting(E_STRICT);
        date_default_timezone_set('Asia/Shanghai'); //设定时区东八区
        $mail = new \PHPMailer(); //new 一个phpmail对象出来
        $content = ereg_replace("[\]", '', $content); //对邮件内容进行过滤

        $mail->CharSet = "UTF-8";          //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP();                  //设定使用  SMTP服务
        $mail->SMTPDebug = 1;              //启用SMTP调式功能                                // 启用SMTP调试功能
        $mail->SMTPAuth = true;           //启用SMTP验证功能

        $mail->Host = self::$MAILHOST;    //SMTP 服务器

        $mail->Port = self::$MAILPORT;    //SMTP服务器的端口号
        $mail->Username = self::$MAILUSERNAME; //SMTP服务器的用户名
        $mail->Password = self::$MAILPASS;  //SMTP服务器的密码
        $mail->SetFrom(self::$MAILFROM, self::$MAILDISPLAYNAME);
        $mail->Subject = $title;              //邮件标题
        $mail->MsgHTML($content);           //邮件内容
        $mail->AddAddress($address, '');     //发送邮件地址
        $mail->IsHTML(true);       // 是否以HTML形式发送，如果不是，请删除此行
        if ($mail->Send()) {
            return true;
        }

        dump($mail->Send());
        exit;
        return false;
    }

}
