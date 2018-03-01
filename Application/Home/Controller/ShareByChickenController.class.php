<?php
namespace Home\Controller;
use Think\Controller;
use Com\Wechat\Wechat;

class ShareByChickenController extends Controller
{
    private $_user_id  = '';
    private $_wx_state = '';

    private $_need_login = array(

        'index',

    );

    public function _initialize() {

        echo "<pre>";
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
                $_GET['redirect_uri'] = 'http://www.lianyangji.io/ShareByChicken';
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
            print_r($invite_user_info);
            // 则添加好友

            // 记录分享状态
        }

    }
}