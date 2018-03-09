<?php

namespace Api\Controller;
/**
 * Created by PhpStorm.
 * User: wangbaojin
 * Date: 18/2/6
 * Time: 下午12:15
 */
class EggcoinRechargeController extends ApiController
{
    private $_reason_type = array(

        1=>'充值',
        2=>'认购',
        3=>'投喂',
        4=>'支出',
        5=>'收益',
        6=>'饲料补扣',
        7=>'支出补扣',
        8=>'赠送',
        9=>'奖励',
    );

    private $_state = array(
        1=>'成功',
        2=>'失败',
        3=>'待处理'
    );

    /*
     * 交易记录
     * */
    public function getUserFeedRecord()
    {
        $user_id = I('get.user_id');

        if(!$user_id) $this->api_error(20001,'请先登录');

        $m = M('ChickenRaiseRecord');

        $map['user_id'] = $user_id;

        $page = (int)I('page');

        $data['page_limit']  = 20;
        $data['total_count'] = $m->where($map)->count();
        $data['total_page']  = ceil($data['total_count']/$data['page_limit']);
        $data['now_page']    = ($page > 0 and $page <= $data['total_page']) ? $page : 1;
        $list = $m->where($map)->page($page,$data['page_limit'])->order('id desc')->select();
        if(!$list) $this->api_error(20003,'暂无交易信息');

        // 处理列表
        foreach ($list as $k=>$v)
        {
            $list[$k]['reason_type_info'] = $this->_reason_type[$v['reason_type']];
            $list[$k]['state_info']       = $this->_state[$v['state']];
            $list[$k]['created_date'] = date('Y-m-d H:i:s',$v['created_at']);
        }

        $data['data'] = $list;
        $this->api_return('success',$data);
    }

   /*
    * 充值
    * */
   public function recharge()
   {

       $not_null_param = array(
           'user_id' => '请先登录',
           'total_price' => '请输个充值金额,不然要充满这么豪的吗?',
       );

       $check_res = check_not_null_param($not_null_param,I('get.'));
       if($check_res) $this->api_error(20001,$check_res);

       // 检查有没有未完成的订单,未完成之前让其先支付前一单(待定)


       // 充值金额
       $total_price = (int)I('get.total_price');
       if($total_price < 0.01) $this->api_error(20002,'充值金额不可为0');

       // 订单信息
       $add_data['user_id']   = I('get.user_id');
       $add_data['created_at']= $add_data['updated_at']  = time();
       $add_data['pay_price'] = $add_data['total_price'] = $total_price;
       $add_data['order_sn']  = order_no();
       $add_data['pay_state'] = 1;
       $add_data['state']     = 1;

       $m      = M('Recharge');
       $info_m = M('RechargeInfo');

       $trans = M();
       $trans->startTrans();
       $res = $m->add($add_data);
       if(!$res)
       {
            $this->api_error(20003,'下单失败');
       }


       $wallet_info = M('Wallet')->where('user_id='.I('get.user_id'))->find();
       if(!$wallet_info)
       {
           $trans->rollback();
           $this->api_error(20004,'获取用户信息失败,请重现登录');
       }

       // 查看用户钱包、有没有欠款,有的话优先补交,剩余的再认购饲料
       $shengyu_price = $add_data['total_price']-$wallet_info['arrears_amount'];

       if($wallet_info['arrears_amount'] > 0)
       {
           $arrears_order['order_id'] = $res;
           $arrears_order['recharge_price'] = $shengyu_price > 0 ? $wallet_info['arrears_amount'] : $add_data['total_price'];
           $arrears_order['goods_id'] = 1; //充值类型：1.补还养鸡现金支出；2.饲料',
           $arrears_order['created_at'] = time();
           $arrears_order['updated_at'] = time();
           $arrears_order['state'] = 1; //待发货
           $arrears_order_res = $info_m->add($arrears_order);
           if(!$arrears_order_res)
           {
               $trans->rollback();
               $this->api_error(20005,'下单失败');
           }
       }

       $feed_order['order_id'] = $res;
       $feed_order['recharge_price'] = $shengyu_price;
       $feed_order['goods_id'] = 2; //充值类型：1.补还养鸡现金支出；2.饲料',
       $feed_order['created_at'] = time();
       $feed_order['updated_at'] = time();
       $feed_order['state'] = 1; //待发货
       $feed_order_res = $info_m->add($feed_order);
       if(!$feed_order_res)
       {
           $trans->rollback();
           $this->api_error(20006,'下单失败');
       }

       // 下单成功后,返回信息给客户端拉起支付
       $trans->commit();
       $this->api_return('success',$add_data);
   }

   /*充值回调,此处错误应该做日志记录,调试先返回信息便于调试*/
   public function rechargeNotifyUrl()
   {
       
       $order_sn = $_GET['order_sn'];
    
       if(!$order_sn) $this->api_error(20001,'订单号错误');

       // 订单、订单详情
       $m      = M('Recharge');
       $info_m = M('RechargeInfo');
       $order = $m->where('order_sn='.$order_sn)->find();
       $order_info = $info_m->where('order_id='.$order['id'])->select();
       if(!$order or !$order_info) $this->api_error(20002,'订单不存在');

       // 用户钱包信息
       $wallet_m    = M('Wallet');
       $wallet_info = $wallet_m->where('user_id='.$order['user_id'])->find();
       if(!$wallet_info)
       {
           $this->api_error(20004,'获取用户信息失败,请重现登录');
       }

       


           $trans = M();
           $trans->startTrans();
           $saveData['updated_at'] = time();
           $saveData['state'] = 3;
           $saveData['pay_state'] = 2;
           $change_res = $m->where('id='.$order['id'])->save($saveData);
           if(!$change_res) $this->api_error(20004,'订单状态修改失败');

           // 记录充值流水
           $raise_record_m = M('ChickenRaiseRecord');
           $raise_record = array();
           $raise_record['user_id'] = $order['user_id'];
           $raise_record['amount']  = $order['total_price'];
           $raise_record['unit']    = '元';
           $raise_record['reason_source_id'] = $order['id'];
           $raise_record['reason_type'] = 1;//事由类型id：1.充值、2.饲料认购，3.饲料消耗；4.药物及其他支出；5.现金收益；6.饲料补扣；7.药物及其他支出补扣'
           $raise_record['reason_narration'] = '蛋鸡养殖饲料';
           $raise_record['created_at'] = time();
           $raise_record['state'] = 1; //状态：1.成功;2.失败;3.待处理
           if(!$raise_record_m->add($raise_record))
           {
               $trans->rollback();
               $this->api_error(20005,'流水记录失败');
           }

           foreach ($order_info as $k=>$v)
           {
               $order_info_data = array();
               $order_info_data['updated_at'] = time();
               $order_info_data['state'] = 3;
               $order_info_res = $info_m->where('id='.$v['id'])->save($order_info_data);
               if(!$order_info_res)
               {
                   $trans->rollback();
                   $this->api_error(20006,'认购失败');
               }

               if($v['goods_id']==1) {// 还款
                   $arrears_res = $wallet_m->where('user_id='.$order['user_id'])->setField('arrears_amount',$wallet_info['arrears_amount']-$v['recharge_price']);
                   if(!$arrears_res)
                   {
                       $trans->rollback();
                       $this->api_error(20006,'还款失败');
                   }

                   // 记录还款流水=
                   $raise_record = array();
                   $raise_record['user_id'] = $order['user_id'];
                   $raise_record['amount']  = abs($v['recharge_price']);
                   $raise_record['unit']    = '元';
                   $raise_record['reason_source_id'] = $order['id'];
                   $raise_record['reason_type'] = 7;//事由类型id：1.充值、2.饲料认购，3.饲料消耗；4.药物及其他支出；5.现金收益；6.饲料补扣；7.药物及其他支出补扣'
                   $raise_record['reason_narration'] = '药物及其他支出补扣';
                   $raise_record['created_at'] = time();
                   $raise_record['state'] = 1; //状态：1.成功;2.失败;3.待处理
                   if(!$raise_record_m->add($raise_record))
                   {
                       $trans->rollback();
                       $this->api_error(20007,'还款流水记录失败');
                   }

               }
               if($v['goods_id']==2) {// 饲料认购

                   //认购饲料数,单位:kg; =  充值金额 / 今日蛋价
                   $feed_price  = M('TodayPrice')->order('delivery_date')->limit(1)->getField('feed_price');
                   $feed_amount = round($v['recharge_price']/$feed_price,2);
                   $feed_res = $wallet_m->where('user_id='.$order['user_id'])->setField('feed_amount',$wallet_info['feed_amount']+$feed_amount);

                   // 饲料认购流水=
                   $raise_record = array();
                   $raise_record['user_id'] = $order['user_id'];
                   $raise_record['amount']  = $feed_amount*1000;
                   $raise_record['unit']    = 'g';
                   $raise_record['reason_source_id'] = $order['id'];
                   $raise_record['reason_type'] = 2;//事由类型id：1.买入、2.饲料买入，3.投喂；4.支出；5.收益；6.饲料补扣；7.支出补扣'
                   $raise_record['reason_narration'] = '饲料认购';
                   $raise_record['created_at'] = time();
                   $raise_record['state'] = 1; //状态：1.成功;2.失败;3.待处理
                   if(!$raise_record_m->add($raise_record) or !$feed_res)
                   {
                       $trans->rollback();
                       $this->api_error(20007,'认购饲料失败');
                   }

                   // 饲料补扣流水记录
                   if($wallet_info['feed_amount'] < 0)
                   {
                       $raise_record = array();
                       $raise_record['user_id'] = $order['user_id'];
                       $raise_record['amount']  = $wallet_info['feed_amount']*1000;
                       $raise_record['unit']    = 'g';
                       $raise_record['reason_source_id'] = $order['id'];
                       $raise_record['reason_type'] = 6;//事由类型id：1.买入、2.饲料买入，3.投喂；4.支出；5.收益；6.饲料补扣；7.支出补扣'
                       $raise_record['reason_narration'] = '饲料补扣';
                       $raise_record['created_at'] = time();
                       $raise_record['state'] = 1; //状态：1.成功;2.失败;3.待处理
                       if(!$raise_record_m->add($raise_record))
                       {
                           $trans->rollback();
                           $this->api_error(20007,'饲料补扣流水记录失败');
                       }
                   }

               }
           }
           $trans->commit();
           $this->api_return('success');
       

       //其他状态
   }
}