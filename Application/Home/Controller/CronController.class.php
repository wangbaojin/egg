<?php
namespace Home\Controller;

namespace Home\Controller;

use Think\Controller;
use Com\PHPMailer\phpmailerAction;

class CronController extends Controller
{
    // 发送邮件
    public function cronSendMail()
    {
        $mail_m = M('SendMail');
        $send_map['send_status'] = 1;
        $send_map['send_times']  = array('lt',time());
        $list = $mail_m->where($send_map)->select();

        $send_m = new phpmailerAction();

        if($list)
        {
            foreach ($list as $k=>$v)
            {
                $res = $send_m->sendMail($v['receiver'],$v['contents'],$v['title']);
                if($res['code']==1)
                {
                    $mail_m->where('id='.$v['id'])->setField('send_status',3);
                    echo 'success','<br>';
                }
                else
                {
                    echo 'error','<br>';
                }
            }
        }
        else
        {
            echo "NULL";
        }
    }
}