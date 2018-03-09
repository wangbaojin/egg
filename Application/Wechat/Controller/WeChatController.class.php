<?php
namespace WeChat\Controller;
use Think\Controller;
use Com\Wechat\Wechat;
use Com\Wechat\Wechatjssdk;

class WeChatController extends Controller
{
    // 微信登录
    public function getWechatInfo()
    {
        echo "<pre>";
        $this->_wx = new Wechat();
        //if(isset($_GET['code']) and $this->checkSignature())
        if(isset($_GET['code'],$_GET['state']))
        {
            $code_res = $this->_wx->getOpenId ($_GET['code']);


            // 先用access_token获取用户信息
            $res = $this->_wx->getUserInfo($code_res['access_token'],$code_res['openid']);

            // 如果access_token超时,则尝试刷新access_token再次获取
            if(!$res['openid'])
            {
                $refresh_res = $this->_wx->refresh($code_res['refresh_token']);
                if($refresh_res['access_token'])
                {
                    $res = $this->_wx->getUserInfo($refresh_res['access_token'], $code_res['openid']);
                }
            }

            // 取得用户信息则返回
            if($res['openid'])
            {
                $wx_state = session('wx_state');

                // 释放session
                session_destroy();
                $stateData = json_decode(base64_decode($wx_state),true);

                if(!$stateData['redirect_uri']) die('回调参数错误');
                header("Location:".urldecode($stateData['redirect_uri']).'?wx_state='.$wx_state.'&wx_info='.base64_encode(json_encode($res)));die;
            }
            else
            {// 未取得则说明需要重新授权
                $conf = array('scope'=>'snsapi_userinfo');// 是否弹出授权页面【 snsapi_userinfo :是，snsapi_base:不 】;
            }
        }
        else
        {
            $conf = array('scope' => 'snsapi_base');// 是否弹出授权页面【 snsapi_userinfo :是，snsapi_base:不 】;
        }
        session('wx_state',$_GET['wx_state']);
        $this->_wx->config($conf);
        $this->_wx->authorize();die;
    }

    private function checkSignature()
    {

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = '85ce0d6a319c09b564e2bd32cc64e38f';
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    /*微信jssdk*/
    public function getSignPackage()
    {
        $url = urldecode($_GET['url']);
        $this->_wxjsdk = new Wechatjssdk();
        $ticket = $this->_wxjsdk->getSignPackage($url);
        echo json_encode($ticket);
    }
}