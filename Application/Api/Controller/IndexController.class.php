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

    public function reOrder()
    {
        $m = M('ChickenOrder');
        $c_m = M('Chicken');
        $map['pay_state'] = 2;
        $map['state'] = 3;
        echo "<pre>";
        //$list = $m->where($map)->field('user_id,chicken_type,num')->order('user_id')->select();
        $new_list = array();
        $user_idAry = $m->where($map)->group('user_id')->getField('user_id',true);
        //print_r($user_idAry);die;
        foreach ($user_idAry as $uv)
        {
            $count_tmp = $count_map = array();
            $count_map['user_id'] = $uv;
            $count_map['pay_state'] = 2;
            $count_map['state'] = 3;
            $count = $m->where($count_map)->field("SUM(num) as num,chicken_type")->group('chicken_type')->select();
            //$count_tmp = $m->where($count_map)->field('user_id,chicken_type,num')->select();
            $count_tmp['user_id'] = $uv;
            $count_tmp['count'] = $count;
            $new_list[] = $count_tmp;
        }
        //print_r($new_list);
        //die;
        //die;
        foreach ($new_list as $ok=>$ov)
        {


            $tmp_map = array();
            $tmp_map['user_id'] = $ov['user_id'];
            $tmp_map['state']   = array('in',array(4,5));
            foreach ($ov['count'] as $oc_k=>$oc_v)
            {
                $tmp_map['chicken_type'] = 1;
                $hd = $c_m->where($tmp_map)->COUNT();
                $new_list[$ok]['count'][$oc_k]['hd1'] = $hd;

                $tmp_map['chicken_type'] = 2;
                $hd = $c_m->where($tmp_map)->COUNT();
                $new_list[$ok]['count'][$oc_k]['hd2'] = $hd;

                $tmp_map['chicken_type'] = $oc_v['chicken_type'];
                $hd = $c_m->where($tmp_map)->COUNT();
                $new_list[$ok]['count'][$oc_k]['hd'] = $hd;
                if($hd != $oc_v['num']) $new_list[$ok]['ERROR'] = '1';
            }
        }
        print_r($new_list);
    }
}