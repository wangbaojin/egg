<?php
/**
 * Created by PhpStorm.
 * User: lkk
 * Date: 18/1/19
 * Time: 下午3:06
 */

namespace Com\PHPMailer;
use Com\PHPMailer\src\PHPMailer;

class phpmailerAction
{
    public function sendMail($receiver,$contents,$title)
    {

        $return_data = array(
            'code' => 1,
            'msg'  => 'Message sent!'
        );

        $mail = new PHPMailer();
        //$mail->SMTPDebug = 2;
        //设置邮件使用SMTP
        $mail->isSMTP();
        // 设置邮件程序以使用SMTP
        $mail->Host = 'smtp.exmail.qq.com';
        // 设置邮件内容的编码
        $mail->CharSet='UTF-8';
        // 启用SMTP验证
        $mail->SMTPAuth = true;

        $mail->Port = 587;

        // SMTP username
        $mail->Username = 'service@jiwo.io';
        // SMTP password
        $mail->Password = 'Lianyangji0207';
        //Set who the message is to be sent from
        $mail->setFrom('service@jiwo.io','链养鸡');
        //Set an alternative reply-to address
        //$mail->addReplyTo('replyto@example.com', 'First Last');
        //Set who the message is to be sent to
        $mail->addAddress($receiver);
        //Set the subject line
        $mail->Subject = $title;
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        //$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
        //Replace the plain text body with one created manually
        $mail->Body = $contents;
        //Attach an image file
        //$mail->addAttachment('images/phpmailer_mini.png');

        //send the message, check for errors
        if (!$mail->send())
        {
            $return_data['code'] = 0;
            $return_data['msg']  = "Mailer Error: " . $mail->ErrorInfo;
        }
        return $return_data;
    }
}