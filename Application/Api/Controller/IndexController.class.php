<?php
namespace Api\Controller;

use Think\Controller;

class IndexController extends ApiController
{
    public function index()
    {
      echo '111';
    }

    /*用户合约*/
    public function provision()
    {
        $this->display();
    }

    /*隐私条款*/
    public function contract()
    {
        $this->display();
    }

    /*关于我们*/
    public function about_us()
    {
        $this->display();
    }

    /*活动*/
    public function activity()
    {
        $this->display();
    }

    /*开关*/
    public function buttonOff()
    {
        $list['state'] = 1;
        $this->api_return('success', $list);
    }
}