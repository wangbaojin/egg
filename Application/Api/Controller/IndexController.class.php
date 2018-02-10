<?php
namespace Api\Controller;

use Think\Controller;

class IndexController extends Controller
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
}