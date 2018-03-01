<?php
/**
 * 任务类
 * Created by PhpStorm.
 * User: lkk
 * Date: 18/2/7
 * Time: 下午11:18
 */

namespace Api\Controller;

class TaskController extends ApiController
{
    /*今日任务*/
    public function todayTask()
    {
        $user_id = I('get.user_id');
        if (!$user_id) $this->api_error(20001, '请先登录');
        $user_info = M('User')->find($user_id);

        // 已领取
        $data['received'] = '0.0g';

        // 待领取
        $data['unreceived'] = '20.0g';

        // 获取分享链接
        $data['share_url'] = 'http://wechat.jiagehao.cn/ShareByChicken/index?invite_code='.$user_info['invite_code'];


        $map['user_id'] = $user_id;
        $map['add_date'] = date('Y-m-d');

        // 获取签到状态
        $data['sign_in'] = M('SignIn')->where($map)->find() ? 1 : 2;
        $data['sign_reward'] = task_reward('sign_reward').'g';


        // 获取分享状态
        $share = M('Share')->where($map)->find();
        $data['share_st'] = $share ? 1 : 2;
        $data['share_reward'] = task_reward('share_reward').'g';

        // 获取好友首次登录
        $data['friend_login_in'] = $share['share_state'] ? 1 : 2;
        $data['friend_login_reward'] = task_reward('friend_login_reward').'g';

        // 数字资产转移
        $data['transfer_success'] = M('TransferSuccess')->where($map)->find() ? 1 : 2;
        $data['transfer_success_reward'] = task_reward('transfer_success_reward').'g';

        $this->api_return('success',$data);
    }

    /*签到*/
    public function signIn()
    {
        $user_id = I('get.user_id');
        if (!$user_id) $this->api_error(20001, '请先登录');

        $m = M('SignIn');
        $map['user_id'] = $user_id;
        $map['add_date'] = date('Y-m-d');
        if($m->where($map)->find())
        {
            $this->api_error(20002,'今日已签到');
        }

        $trans = M();
        $trans->startTrans();

        // 签到
        $res = $m->add($map);
        if(!$res)
        {
            $this->api_error(20003,'啊偶!系统出问题了,请稍后重试');
        }

        // 赠送饲料奖励 20g 饲料
        $feed_amount = task_reward('sign_reward');

        $wallet_m = M('Wallet');
        $wallet_map['user_id'] = $user_id;
        $wallet_info           = $wallet_m->where($wallet_map)->find();
        if(!$wallet_info)
        {
            $add_data['feed_amount'] = $feed_amount/1000;// 单位kg
            $add_data['user_id']     = $user_id;
            $wallet_res = $wallet_m->add($add_data);
        }
        else
        {
            $wallet_res = $wallet_m->where($wallet_map)->setInc('feed_amount',$feed_amount/1000);// 单位kg
        }

        if(!$wallet_res)
        {
            $trans->rollback();
            $this->api_error(20004,'啊偶!获取奖励失败,请稍后重试');
        }

        // 添加赠送饲料流水
        $record = array();
        $record['user_id'] = $user_id;
        $record['amount']  = $feed_amount;
        $record['reason_source_id'] = $res;
        $record['reason_type'] = 9;//1=>'充值',2=>'认购',3=>'投喂',4=>'支出',5=>'收益',6=>'饲料补扣',7=>'支出补扣',8=>'赠送',9=>' 任务奖励',
        $record['reason_narration'] = '签到';
        $record['state'] = 1;//状态：1.成功;2.失败;3.待处理
        $record['unit'] = 'g';
        $record = addRecord($record);
        if(!$record)
        {
            $trans->rollback();
            $this->api_error(20004,'啊偶!获取奖励失败,请稍后重试');
        }
        $trans->commit();
        $this->api_return('success');
    }

    /* 分享成功 */
    public function shareSuccess()
    {
        $user_id = I('get.user_id');
        if (!$user_id) $this->api_error(20001, '请先登录');

        $m = M('Share');
        $map['user_id'] = $user_id;
        $map['add_date'] = date('Y-m-d');
        if($m->where($map)->find())
        {
            $this->api_error(20002,'今日已领取');
        }

        $trans = M();
        $trans->startTrans();

        // 签到
        $res = $m->add($map);
        if(!$res)
        {
            $this->api_error(20003,'啊偶!系统出问题了,请稍后重试');
        }

        // 赠送饲料奖励 20g 饲料
        $feed_amount = task_reward('share_reward');

        $wallet_m = M('Wallet');
        $wallet_map['user_id'] = $user_id;
        $wallet_info           = $wallet_m->where($wallet_map)->find();
        if(!$wallet_info)
        {
            $add_data['feed_amount'] = $feed_amount/1000;// 单位kg
            $wallet_res = $wallet_m->add($add_data);
        }
        else
        {
            $wallet_res = $wallet_m->where($wallet_map)->setInc('feed_amount',$feed_amount/1000);// 单位kg
        }

        if(!$wallet_res)
        {
            $trans->rollback();
            $this->api_error(20004,'啊偶!获取奖励失败,请稍后重试');
        }


        // 添加赠送饲料流水
        $record = array();
        $record['user_id'] = $user_id;
        $record['amount']  = $feed_amount;
        $record['reason_source_id'] = $res;
        $record['reason_type'] = 9;//1=>'充值',2=>'认购',3=>'投喂',4=>'支出',5=>'收益',6=>'饲料补扣',7=>'支出补扣',8=>'赠送',9=>' 任务奖励',
        $record['reason_narration'] = '分享';
        $record['state'] = 1;//状态：1.成功;2.失败;3.待处理
        $record['unit'] = 'g';
        $record = addRecord($record);
        if(!$record)
        {
            $trans->rollback();
            $this->api_error(20004,'啊偶!获取奖励失败,请稍后重试');
        }
        $trans->commit();
        $this->api_return('success');
    }


    /* 数字资产转移成功领取奖励 */
    public function eggCoinTransferSuccess()
    {
        $user_id = I('get.user_id');
        if (!$user_id) $this->api_error(20001, '请先登录');

        $m = M('TransferSuccess');
        $map['user_id']  = $user_id;
        $map['add_date'] = date('Y-m-d');
        $map['state']    = 1;
        $map['num']      = 1;
        if($m->where($map)->find())
        {
            $this->api_error(20002,'今日已领取');
        }

        $trans = M();
        $trans->startTrans();

        // 签到
        $res = $m->add($map);
        if(!$res)
        {
            $this->api_error(20003,'啊偶!系统出问题了,请稍后重试');
        }

        // 赠送饲料奖励 20g 饲料
        $feed_amount = task_reward('transfer_success_reward');

        $wallet_m = M('Wallet');
        $wallet_map['user_id'] = $user_id;
        $wallet_info           = $wallet_m->where($wallet_map)->find();
        if(!$wallet_info)
        {
            $add_data['feed_amount'] = $feed_amount/1000;// 单位kg
            $wallet_res = $wallet_m->add($add_data);
        }
        else
        {
            $wallet_res = $wallet_m->where($wallet_map)->setInc('feed_amount',$feed_amount/1000);// 单位kg
        }

        if(!$wallet_res)
        {
            $trans->rollback();
            $this->api_error(20004,'啊偶!获取奖励失败,请稍后重试');
        }


        // 添加赠送饲料流水
        $record = array();
        $record['user_id'] = $user_id;
        $record['amount']  = $feed_amount;
        $record['reason_source_id'] = $res;
        $record['reason_type'] = 9;//1=>'充值',2=>'认购',3=>'投喂',4=>'支出',5=>'收益',6=>'饲料补扣',7=>'支出补扣',8=>'赠送',9=>' 任务奖励',
        $record['reason_narration'] = '资产转移';
        $record['state'] = 1;//状态：1.成功;2.失败;3.待处理
        $record['unit'] = 'g';
        $record = addRecord($record);
        if(!$record)
        {
            $trans->rollback();
            $this->api_error(20004,'啊偶!获取奖励失败,请稍后重试');
        }
        $trans->commit();
        $this->api_return('success');
    }

    /*邀请好友购买*/
    public function inviteFriendBuy()
    {
        $user_id   = I('get.user_id');
        $friend_id = I('get.friend_id');
        if (!$user_id or !$friend_id) $this->api_error(20001, '参数错误');

        $m = M('InviteBuy');
        $map['invite_user_id'] = $user_id;
        $map['user_id']        = $friend_id;
        $map['add_date']       = date('Y-m-d');
        if($m->where($map)->find())
        {
            $this->api_error(20002,'今天已经邀请过TA了噢!');
        }

        $trans = M();
        $trans->startTrans();

        // 邀请
        $res = $m->add($map);
        if(!$res)
        {
            // 给好友发送邀请信息、待写
            $this->api_error(20003,'啊偶!系统出问题了,请稍后重试');
        }
        $trans->commit();
        $this->api_return('success');
    }
}