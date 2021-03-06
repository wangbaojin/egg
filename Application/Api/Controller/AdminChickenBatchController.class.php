<?php
/**
 * Created by PhpStorm.
 * User: lkk
 * Date: 17/11/13
 * Time: 下午5:19
 */

namespace Api\Controller;

use Think\Controller;

class AdminChickenBatchController extends AdminPublicController
{
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        // 实例hua
        $this->_m = M($this->_table_name);
    }

    public function index()
    {
        // 分页
        $map = array();
        if(I('get.name')) $map['name'] = array('like',"%".I('get.name')."%");
        $page = (int)I('page');
        $data['page_limit']  = 20;
        $data['total_count'] = $this->_m->where($map)->count();
        $data['total_page']  = ceil($data['total_count']/$data['page_limit']);
        $data['now_page']    = ($page > 0 and $page <= $data['total_page']) ? $page : 1;
        $list = $this->_m->where($map)->page($page,$data['page_limit'])->order('id')->select();

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
            if($data['amount']<1 or $data['amount']>999999) die('发行数量超出!');
            if($this->_m->where('out_code="'.$data['out_code'].'"')->find()) die('对外批次编码必须唯一,'.$data['out_code'].'的编码已被使用!');
            $data['create_time'] = time();
            $data['start_time']  = strtotime($data['start_time']);
            $data['end_time']    = strtotime($data['end_time']);
            $trans = M();
            $trans->startTrans();

            // 是否默认
            if($data['is_default']=='on')
            {
                $this->_m->where('is_default=1')->setField('is_default',2);
                $data['is_default']=1;
            }
            else
            {
                $data['is_default']=2;
            }
            $res = $this->_m->add($data);
            if(!$res)
            {
                $trans->rollback();
                die('添加失败');
            }

            // 生成鸡
            $i = 1;
            $y = 1;
            while ($i<=$data['amount'])
            {
                $tmp = array();
                $tmp['user_id'] = 0;
                $tmp['chicken_code']  = $data['out_code'].str_pad($i,6,0,STR_PAD_LEFT);
                $tmp['chicken_batch'] = $res;
                $tmp['state']   = 1;
                $chicken_data[] = $tmp;
                unset($tmp);
                if($y>10000 or ($i==$data['amount']))
                {
                    $add_res = M('Chicken')->addAll($chicken_data);
                    if(!$add_res)
                    {
                        $trans->rollback();
                        die('鸡舍鸡入栏失败');
                    }
                    unset($chicken_data);
                    $chicken_data = array();
                    $y=1;
                }
                $i++;
                $y++;
            }
            $trans->commit();
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
            $data['start_time']  = strtotime($data['start_time']);
            $data['end_time']    = strtotime($data['end_time']);
            // 是否默认
            if($data['is_default']=='on')
            {
                $this->_m->where('is_default=1')->setField('is_default',2);
                $data['is_default']=1;
            }
            else
            {
                $data['is_default']=2;
            }
            if(!$this->_m->where('id='.$data['id'])->save($data)) die('添加失败');
            die('success');
        }

        $info = $this->_m->where('id='.$id)->find();
        if(!$info) die('该批次不存在!');

        $info = $this->disposeData($info);
        $this->assign('info',$info);
        $this->display();
    }

    public function disposeData($arr)
    {
        // 鸡认购状态
        $chicken_state = M('Chicken')->where('chicken_batch='.$arr['id'])->group('state')->field(array("count(state)"=>"count","state"))->select();

        foreach (C('CHICKEN_STATE') as $k=>$v)
        {
            $chicken_state_tmp = array();
            $chicken_state_tmp['id'] = $k;
            $chicken_state_tmp['state_info'] = $v;
            if($chicken_state){
            foreach ($chicken_state as $fk=>$fv)
            {
                if($k==$fv['state'])
                {
                    $chicken_state_tmp['count'] = $fv['count'];
                    break;
                }
            }}
            if(!$chicken_state_tmp['count']) $chicken_state_tmp['count'] = 0;

            $chicken_state_info[] =$chicken_state_tmp;
        }
        $arr['chicken_state_info'] = $chicken_state_info;
        $arr['sold_out'] = M('Chicken')->group('state')->select();

        // 发行日期
        $arr['start_date'] = date('Y-m-d H:i:s',$arr['start_time']);
        $arr['end_date']   = date('Y-m-d H:i:s',$arr['end_time']);


        if($arr['is_default']==1) $arr['is_default_info']='是';
        return $arr;
    }
}