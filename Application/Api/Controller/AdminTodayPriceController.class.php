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
        // 分页
        $map = array();
        if(I('get.delivery_date')) $map['delivery_date'] = I('get.delivery_date');
        $page = (int)I('page');
        $data['page_limit']  = 20;
        $data['total_count'] = $this->_m->where($map)->count();
        $data['total_page']  = ceil($data['total_count']/$data['page_limit']);
        $data['now_page']    = ($page > 0 and $page <= $data['total_page']) ? $page : 1;
        $list = $this->_m->where($map)->page($page,$data['page_limit'])->order('id desc')->select();

        // 处理数据
        foreach ($list as $k=>$v)
        {
            $list[$k] = $this->disposeData($v);
        }

        $Page       = new \Think\Page($data['total_count'],$data['page_limit']);
        $show       = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);
        $this->assign('count',$data['total_count']);
        $this->display();
    }

    public function add()
    {
        $data = I('post.');

        if($data) {
            if($this->_m->where('delivery_date="'.$data['delivery_date'].'"')->find()) die($data['delivery_date'].'的价格已经添加!');
            $data['updated_at'] = time();
            if(!$this->_m->add($data)) die('添加失败');
            die('success');
        }
        $this->assign('delivery_date',date('Y-m-d'));
        $this->display();
    }

    public function edit()
    {
        $id   = I('get.id');
        $data = I('post.');

        if($data) {
            $data['updated_at'] = time();
            if(!$this->_m->where('id='.$data['id'])->save($data)) die('添加失败');
            die('success');
        }

        $info = $this->_m->where('id='.$id)->find();
        if(!$info) die($data['delivery_date'].'的价格已经添加!');

        $this->assign('info',$info);
        $this->display();
    }

    public function disposeData($arr)
    {
        $arr['updated_date'] = date('Y-m-d H:i:s',$arr['updated_at']);
        return $arr;
    }
}