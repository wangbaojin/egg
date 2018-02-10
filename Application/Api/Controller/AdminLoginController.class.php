<?php
/**
 * Created by PhpStorm.
 * User: lkk
 * Date: 17/11/13
 * Time: 下午5:19
 */

namespace Api\Controller;

use Think\Controller;

class AdminLoginController extends Controller
{
    public function login()
    {
        if(I('post.'))
        {
            $m = M('AdminUser');
            $m->create();
            $map['mobile']  = I('post.mobile');
            $map['pass_wd'] = md5(I('post.pass_wd'));
            $map['user_st'] = 1;
            $info = $m->where($map)->find();
            if($info)
            {
                $name = $info['full_name'] ? $info['full_name'] : $info['mobile'];
                session('uid',$info['id']);
                session('name',$name);
                $msg = 'success';
            }
            else
            {
                $msg = 'error';
            }
            die($msg);
        }
        $this->display('login');
    }
}