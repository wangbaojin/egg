<?php
/**
 * Created by PhpStorm.
 * User: lkk
 * Date: 17/11/13
 * Time: 下午5:19
 */

namespace Api\Controller;

use Think\Controller;

class AdminChickenOrderController extends AdminPublicController
{
    private $pay_state_info = array(
        1=>'待支付',
        2=>'支付完成',
        3=>'退款中',
        4=>'已退款',
        5=>'支付金额异常'
    );
    private $state_info = array(
        1=>'待发货',
        2=>'已发货',
        3=>'已完成',
        4=>'已结束',
    );
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
        // 商品详情
        $chicken_type = M('ChickenType')->find($arr['chicken_type']);
        $arr['goods_info'] = $chicken_type['name'].'<br><s>'.$chicken_type['price'].'元</s><b>'.($chicken_type['price']-$chicken_type['discount']).'</b>元'.' X '.$arr['num'];

        $chicken_batch = M('ChickenBatch')->find($arr['chicken_batch']);
        $arr['chicken_batch'] = $chicken_batch;
        // 状态
        $arr['pay_state_info'] = $this->pay_state_info[$arr['pay_state']];
        $arr['state_info']     = $this->state_info[$arr['state']];

        // 用户信息
        $userInfo = getUserInfoByUserId($arr['user_id']);
        $arr['user_info'] = $userInfo['data'];
        // 鸡认购状态
        return $arr;
    }
}