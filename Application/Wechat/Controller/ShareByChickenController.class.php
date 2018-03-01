<?php
namespace Wechat\Controller;
use Think\Controller;
use Com\Wechat\Wechat;

class ShareByChickenController extends Controller
{
    private $_user_id  = '';
    private $_wx_state = '';

    private $_need_login = array(
        'index',
    );

    public function _initialize()
    {
        //判断接口是否需要微信登录
        $this->_user_id = session('user_id');
        $res = in_array(ACTION_NAME,$this->_need_login);
        if(!$this->_user_id && in_array(ACTION_NAME,$this->_need_login))
        {
            if($_GET['wx_info'])
            {
                $wx_info = json_decode(base64_decode($_GET['wx_info']),true);
                if(!$wx_info) die('请在微信授权登录');

                $wechat_data = array();
                $wechat_data['wx_open_id']                            = $wx_info['openid'];
                if($wx_info['headimgurl'])   $wechat_data['wx_pic']   = $wx_info['headimgurl'];
                if($wx_info['nickname'])     $wechat_data['wx_nick_name'] = base64_encode($wx_info['nickname']);
                if($wx_info['unionid'])      $wechat_data['unionid']  = $wx_info['unionid'];
                if($wx_info['sex'])          $wechat_data['sex']      = $wx_info['sex'];//sex	用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
                if($wx_info['province'])     $wechat_data['province'] = $wx_info['province'];
                if($wx_info['city'])         $wechat_data['city']     = $wx_info['city'];
                if($wx_info['country'])      $wechat_data['country']  = $wx_info['country'];
                $res = addUserByWechat($wechat_data);
                if($res['code']!=1 and $res['code']!=20003) die('请在微信授权登录');

                // 记录登录
                session('user_id',$res['data']['user_id']);
                $this->_user_id = $res['data']['user_id'];
            }
            else
            {
                $_GET['redirect_uri'] = 'http://wechat.jiagehao.cn/ShareByChicken';
                $url = 'http://wechat.jiagehao.cn/WeChat/getWechatInfo?'.'wx_state='.base64_encode(json_encode($_GET));
                header("Location:".$url);die;
            }
        }
        $this->_wx_state = $_GET['wx_state'] ? json_decode(base64_decode($_GET['wx_state']),true) : $_GET;
    }

    // 分享首页
    public function index()
    {

        $m = M('User');
        $user_info = $m->where('state=1')->find($this->_user_id);
        if(!$user_info) die('该用户不存在');

        if($this->_wx_state['invite_code'])  $invite_user_info = $m->where('invite_code="'.$this->_wx_state['invite_code'].'"')->find();

        if($invite_user_info && ($user_info['id'] != $invite_user_info['id']))
        {
            // 则添加好友
            $f_m = M('Friends');
            $f_map_u['user_id']   = $user_info['id'];
            $f_map_u['friend_id'] = $invite_user_info['id'];
            if(!$f_m->where($f_map_u)->find())
            {
                $f_map_u['add_date'] = date('Y-m-d');
                $f_m->add($f_map_u);
            }

            $f_map_f['user_id']   = $invite_user_info['id'];
            $f_map_f['friend_id'] = $user_info['id'];
            if(!$f_m->where($f_map_f)->find())
            {
                $f_map_f['add_date'] = date('Y-m-d');
                $f_m->add($f_map_f);
            }

            // 记录邀请登录状态
            $share_m =M('Share');
            $share_map['user_id']  = $invite_user_info['id'];
            $share_map['add_date'] = date('Y-m-d');
            $today_share = $share_m->where($share_map)->find();
            if($today_share)
            {
                // 如果是今日好友第一次登录,则记录邀请人每日邀请好友登录奖励
                if($today_share['share_state']==2)
                {
                    $trans = M();
                    $trans->startTrans();

                    // 登录状态
                    $share_res = $share_m->where('id='.$today_share['id'])->setField('share_state',1);
                    if(!$share_res)
                    {
                        $this->api_error(20003,'啊偶!系统出问题了,请稍后重试');
                    }

                    // 赠送饲料奖励 20g 饲料
                    $feed_amount = 20;

                    $wallet_m = M('Wallet');
                    $wallet_map['user_id'] = $invite_user_info['id'];
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
                    $record['user_id'] = $invite_user_info['id'];
                    $record['amount']  = $feed_amount;
                    $record['reason_source_id'] = $today_share['id'];
                    $record['reason_type'] = 9;//1=>'充值',2=>'认购',3=>'投喂',4=>'支出',5=>'收益',6=>'饲料补扣',7=>'支出补扣',8=>'赠送',9=>' 任务奖励',
                    $record['reason_narration'] = '邀请好友登录';
                    $record['state'] = 1;//状态：1.成功;2.失败;3.待处理
                    $record['unit'] = 'g';
                    $record = addRecord($record);
                    if(!$record)
                    {
                        $trans->rollback();
                        $this->api_error(20004,'啊偶!获取奖励失败,请稍后重试');
                    }
                    $trans->commit();
                }
                else
                {
                    $share_m->where('id='.$today_share['id'])->setInc('share_num',1);
                }
            }
        }
        $this->assign('invite_code',$this->_wx_state['invite_code']);
        $this->display();
    }
}