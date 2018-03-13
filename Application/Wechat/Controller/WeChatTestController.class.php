<?php
namespace Wechat\Controller;
use Com\Wechat\WechatMedia;
use Think\Controller;
use Com\Wechat\Wechat;
use Com\Wechat\Wechatjssdk;
use Com\Wechat\WechatPay;
use Com\Qrcode\Qr;

class WeChatTestController extends PublicController
{
    private $_user_id  = '';
    private $_wx_state = '';

    private $_need_login = array(
        'index',
        'pay',
        'weixinpay',
        'buy',
    );

    public function _initialize()
    {

        // 判断用户
        $this->_user_id = session('user_id');
        $user_info = M('User')->where('user_st=1')->find($this->_user_id);
        if(!$user_info && $this->_user_id)
        {
            $this->_user_id = '';
            session_destroy();
        }

        //判断接口是否需要微信登录
        $res = in_array(ACTION_NAME,$this->_need_login);

        if(!$this->_user_id && in_array(ACTION_NAME,$this->_need_login))
        {
            // 回调地址
            $redirect_uri = 'http://wechat.jiagehao.cn/ShareByChicken';

            if($_GET['wx_info'])
            {
                $wx_info = json_decode(base64_decode($_GET['wx_info']),true);
                if(!$wx_info) die('请在微信授权登录');

                $wechat_data = array();
                //$wechat_data['wx_open_id']                            = $wx_info['openid'];
                if($wx_info['headimgurl'])   $wechat_data['wx_pic']   = $wx_info['headimgurl'];
                if($wx_info['nickname'])     $wechat_data['wx_nick_name'] = base64_encode($wx_info['nickname']);
                if($wx_info['unionid'])      $wechat_data['unionid']  = $wx_info['unionid'];
                if($wx_info['sex'])          $wechat_data['sex']      = $wx_info['sex'];//sex	用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
                if($wx_info['province'])     $wechat_data['province'] = $wx_info['province'];
                if($wx_info['city'])         $wechat_data['city']     = $wx_info['city'];
                if($wx_info['country'])      $wechat_data['country']  = $wx_info['country'];
                if($wx_info['openid'])       $wechat_data['openid']   = $wx_info['openid'];
                $res = addUserByWechat($wechat_data);
                if($res['code']!=1 and $res['code']!=20003) die('请在微信授权登录'.$res['msg']);

                // 记录登录
                $this->_user_id = $res['data']['user_id'];
                session('user_id',$res['data']['user_id']);
                //header("Location:".$redirect_uri);die;
            }
            else
            {
                $_GET['redirect_uri'] = $redirect_uri;
                $url = 'http://wechat.jiagehao.cn/WeChat/getWechatInfo?'.'wx_state='.base64_encode(json_encode($_GET));
                header("Location:".$url);die;
            }
        }
        $this->_wx_state = $_GET['wx_state'] ? json_decode(base64_decode($_GET['wx_state']),true) : $_GET;
    }

    public function test()
    {
        $m = new WechatMedia();
        $res = $m->batchgetMaterial('image',1,10);
        echo "<pre>";
        print_r($res);
    }
}