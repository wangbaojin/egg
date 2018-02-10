<?php
/**
 * Created by PhpStorm.
 * User: lkk
 * Date: 17/11/13
 * Time: 下午5:19
 */

namespace Api\Controller;

use Think\Controller;

class AdminPublicController extends Controller
{
    public function _initialize()
    {
        // 验证登录
        $uid = session('uid');
        if(!$uid) redirect(U('AdminLogin/login'),2, '还未登录,正在前往登录...');
        $this->assign('admin_name',session('name'));
    }
}