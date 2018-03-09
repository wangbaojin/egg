<?php
namespace Api\Controller;
/**
 * Created by PhpStorm.
 * User: wangbaojin
 * Date: 18/2/6
 * Time: 下午12:15
 */
class EggcoinWithdrawalsController extends ApiController
{
    private $_pay_state = array(
        1=>'待确认',
        2=>'退款中',
        3=>'拒绝',
        4=>'已退款',
        5=>'部分退款',
        6=>'退款失败',
    );

    private $_state = array(
        1=>'申请退款',
        2=>'已完成',
        3=>'撤销'
    );

    /*
     * 交易记录
     * */
    public function apply_withdrawals()
    {
        $not_null_param = array(
            'user_id'       => '请先登录',
            'zhifubao_account' => '请输入支付宝账号',
            'apply_amount' => '您要提多少钱呢?',
        );
        $data = I('post.');
        $check_res = check_not_null_param($not_null_param,$data);
        if($check_res) $this->api_error(20001,$check_res);

        $withdrawals_amount = array(100,50,20);
        if(!in_array($data['apply_amount'],$withdrawals_amount)) $this->api_error(20002,'请输入正确的提现金额');


        $m = M('Withdrawals');
        $w_m = M('Wallet');
        $map['user_id'] = $data['user_id'];
        $wallet = $w_m->where($map)->find();
        if(!$wallet) $this->api_error(20003,'获取用户钱包信息失败,请尝试重现登录');

        // 检查可提取金额
        if($wallet['amount'] < $data['apply_amount']) $this->api_error(20004,'可提取金额不足');

        $trans = M();
        $trans->startTrans();

        // 添加申请
        $add_data['order_sn'] = order_no();
        $add_data['created']  = $add_data['updated']      = time();
        $add_data['pay_price']= $add_data['apply_amount'] = $data['apply_amount'];
        $add_data['pay_state']= 1;
        $add_data['state']    = 1;
        $add_data['user_id']  = $data['user_id'];
        $add_data['apply_amount']     = $data['apply_amount'];
        $add_data['zhifubao_account'] = $data['zhifubao_account'];
        if(!$m->add($add_data))
        {
            $trans->rollback();
            $this->api_error(20005,'啊偶!服务器开小差了,请稍后重试');
        }

        // 冻结金额
        $wallet_change_data['amount'] = $wallet['amount'] - $data['apply_amount'];
        $wallet_change_data['freezing_amount'] = $data['apply_amount']+$wallet['freezing_amount'];
        if(!$w_m->where('amount >= '.$data['apply_amount'].' and user_id='.$data['user_id'])->save($wallet_change_data))
        {
            $trans->rollback();
            $this->api_error(20005,'啊偶!申请提现金额失败,请稍后重试');
        }
        $trans->commit();
        $this->api_return('success');
    }
}