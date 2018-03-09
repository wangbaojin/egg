<?php
/**
 * 任务类
 * Created by PhpStorm.
 * User: lkk
 * Date: 18/2/7
 * Time: 下午11:18
 */

namespace Api\Controller;
use Com\Qrcode\Qr;
use Com\Wechat\Wechatjssdk;

class TaskController extends ApiController
{
    /*今日任务*/
    public function todayTask()
    {
        $user_id = I('get.user_id');
        if (!$user_id) $this->api_error(20001, '请先登录');
        $user_info = M('User')->find($user_id);
        if(!$user_info) $this->api_error(20002,'用户不存在');

        // 获取分享链接
        $data['share_url'] = 'http://wechat.jiagehao.cn/ShareByChicken/index?invite_code='.$user_info['invite_code'];
        $data['share_img'] = 'http://wechat.jiagehao.cn/'.$this->getUserShareImg($user_id);

        $map['user_id'] = $user_id;
        $map['add_date'] = date('Y-m-d');

        // 已领取
        $data['received'] = 0.0;

        // 待领取
        $data['unreceived'] = 0.0;

        // 获取签到状态
        if(M('SignIn')->where($map)->find())
        {
            $data['sign_in'] = 1;
            $data['received'] += task_reward('sign_reward');
        }
        else
        {
            $data['sign_in'] = 2;
            $data['unreceived'] += task_reward('sign_reward');
        }
        $data['sign_reward'] = task_reward('sign_reward').'g';


        // 获取分享状态
        $share = M('Share')->where($map)->find();
        if($share)
        {
            $data['share_st'] = 1;
            $data['received'] += task_reward('share_reward');
        }
        else
        {
            $data['share_st'] = 2;
            $data['unreceived'] += task_reward('share_reward');
        }
        $data['share_reward'] = task_reward('share_reward').'g';

        // 获取好友首次登录
        if($share['share_state']==1)
        {
            $data['friend_login_in'] = 1;
            $data['received'] += task_reward('friend_login_reward');
        }
        else
        {
            $data['friend_login_in'] = 2;
            $data['unreceived'] += task_reward('friend_login_reward');
        }
        $data['friend_login_reward'] = task_reward('friend_login_reward').'g';

        // 数字资产转移
        if(M('TransferSuccess')->where($map)->find())
        {
            $data['transfer_success'] = 1;
            $data['received'] += task_reward('transfer_success_reward');
        }
        else
        {
            $data['transfer_success'] = 2;
            $data['unreceived'] += task_reward('transfer_success_reward');
        }
        $data['transfer_success_reward'] = task_reward('transfer_success_reward').'g';

        $this->api_return('success',$data);
    }

    public function getUserShareImg($user_id='')
    {
        $user_id = I('get.user_id') ? I('get.user_id') : $user_id;
        if (!$user_id) $this->api_error(20001, '请先登录');
        $user_info = M('User')->find($user_id);
        if(!$user_info) die('用户不存在!');

        // 获取分享链接
        $share_url = 'http://wechat.jiagehao.cn/ShareByChicken/index?invite_code='.$user_info['invite_code'];

        $root = './Public/images/Api/share_qr/';
        $qr_outfile = $root.'create_qr_'.$user_info['invite_code'].'.png';
        $share_png   = $root.'share_'.$user_info['invite_code'].'.png';
        $tmp_png   = './Public/images/Api/share.png';

        if(!is_file($share_png)){
            $m = new Qr();
            $config = array(
                '_qr_outfile' => $qr_outfile,
                'level' => 'H',
            );
            $m->config($config);
            $m->create($share_url);


            // 给二维码图片加水印

            $image = new \Think\Image();

            // icon处理
            //$image->open($icon_png)->thumb(50,50)->save($icon_tmp_png);

            // 加icon水印
            //$image->open($config['_qr_outfile']);
            //$image->water($icon_tmp_png,5,100)->save($create_qr_png);
            echo $qr_outfile;
            $image->open($qr_outfile)->thumb(185,185)->save($qr_outfile);

            $image->open($tmp_png);
            $image->water($qr_outfile,array(68,1060),100)->save($share_png);
            $image->open($share_png)->thumb(1024,1024)->save($share_png);
        }

        if($_GET['debug']) {
            ECHO "<IMG SRC='" . substr($share_png, 1) . "' width='500px'>";
        }
        return substr($share_png,1);
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
        $user_info = getUserInfoByUserId($user_id);
        if(!$user_info or $user_info['code']!=1) $this->api_error(20004,'获取用户信息失败,请重新登录');

        $friend_id = I('get.friend_id');
        if (!$user_id or !$friend_id) $this->api_error(20001, '参数错误');

        $m = M('InviteBuy');
        $map['invite_user_id'] = $user_id;
        $map['user_id']        = $friend_id;
        $map['add_date']       = date('Y-m-d');
        $map['add_time']       = time();
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

        // 发送邀请信息
        $mobile = M('User')->where('id='.$friend_id)->getField('mobile');
        $openid = M('UserWechatinfo')->where('user_id='.$friend_id)->getField('openid');
        if($mobile) send_sms($mobile,'您的好友'.$user_info['data']['full_name'].'邀请你一起来区块链养鸡,快来领养鸡赚取收益吧!');
        if($openid) $this->sendTemplateMsg($openid);

        $trans->commit();
        $this->api_return('success');
    }

    public function test()
    {
        $openid = M('UserWechatinfo')->where('user_id=24')->getField('openid');
        $this->sendTemplateMsg($openid);
    }

    // 发送模版消息
    function sendTemplateMsg($openid='oCA61jkiwssJ36igRtgovvCg45bo',$template_id='aku34mBdGuLJTcz5nFOB7bXHtWSBLuGtEEuoyw5Xo5Q',$data=array())
    {
        echo $openid;
        $data = array(
            'first'=> array(
                'value' => '您的好友邀请你一起来链养鸡',
                'color' => '#173177',
            ),
            'keyword1'=> array(
                'value' => 'teemo',
                'color' => '#173177',
            ),
            'keyword2'=> array(
                'value' => 'teemo19788',
                'color' => '#173177',
            ),
            'keyword3'=> array(
                'value' => date('Y年m月d日 H时i分'),
                'color' => '#173177',
            ),
            'remark'=> array(
                'value' => '点击详情，快来领养鸡赚取收益吧',
                'color' => '#173177',
            )
        );
        $arr = array(
            'touser' => $openid,
            'template_id' => $template_id,
            'data' => $data
        );
        $arr['touser'] = $openid;

        $m = new Wechatjssdk();
        $res = $m->send_template_msg($arr);
        //print_r($res);
    }
}