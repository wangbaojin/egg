<?php
/**
 * Created by PhpStorm.
 * User: lkk
 * Date: 17/11/13
 * Time: 下午5:19
 */

namespace Api\Controller;

use Think\Controller;

class AdminIndexController extends AdminPublicController
{
    public function index()
    {
        $this->display();
    }
}