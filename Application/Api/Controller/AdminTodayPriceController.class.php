<?php
/**
 * Created by PhpStorm.
 * User: lkk
 * Date: 17/11/13
 * Time: 下午5:19
 */

namespace Api\Controller;

use Think\Controller;

class AdminTodayPriceController extends AdminPublicController
{
    public function index()
    {
        $m = M('TodayPrice');

        // 分页
        $map = array();
        $page = (int)I('page');
        $data['page_limit']  = 20;
        $data['total_count'] = $m->where($map)->count();
        $data['total_page']  = ceil($data['total_count']/$data['page_limit']);
        $data['now_page']    = ($page > 0 and $page <= $data['total_page']) ? $page : 1;
        $list = $m->where($map)->page($page,$data['page_limit'])->order('id desc')->select();
        print_r($list);die;
        $this->assing('list',$list);
        $this->display();
    }
}