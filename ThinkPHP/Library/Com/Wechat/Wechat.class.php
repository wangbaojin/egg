<?php
namespace Com\Wechat;
/**
 * Created by PhpStorm.
 * User: lkk
 * Date: 18/2/24
 * Time: 下午1:30
 */
class Wechat {

    private $_weixin_connect_path = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    private $_weixin_sns_path = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    private $_weixin_refresh_path = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';
    private $_weixin_userinfo_path = 'https://api.weixin.qq.com/sns/userinfo';
    public function __construct() {
        $this->appid        = 'wxbde5adebd8d6924a';  //公共账号 appid
        $this->secret       = '8f8823b8f1b45bcb609089ac946bec21';  //公众账号AppSecret
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $this->redirect_uri = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        #$this->redirect_uri = urlencode(WEIXIN_REDIRECT_URI); //授权后的回调地址
        $this->scope = 'snsapi_userinfo';   // 是否弹出授权页面【 snsapi_userinfo ，snsapi_base 】;
        $this->state = '';

    }

    public function config($arr) {
        foreach ($arr as $_k => $_v)
            if (isset($this->$_k))
                $this->$_k = $_v;
    }

    public function authorize() {

        $url = "{$this->_weixin_connect_path}?appid={$this->appid}&redirect_uri={$this->redirect_uri}&response_type=code&scope={$this->scope}&state={$this->state}#wechat_redirect";
        header("Location:{$url}");die;
    }

    //获取用户OpenId
    public function getOpenId($code, $grant_type='authorization_code') {
        $url = "{$this->_weixin_sns_path}?appid={$this->appid}&secret={$this->secret}&code={$code}&grant_type=authorization_code";
        #Util::log($url);die;
        $str = file_get_contents($url);
        if ($str)
            return json_decode($str, 1);
        return array('errcode' => '-2', 'errmsg' => '网络错误');
    }

    //超时刷新
    public function refresh($refresh_token) {
        $url = "{$this->_weixin_refresh_path}?appid={$this->appid}&grant_type=refresh_token&refresh_token={$refresh_token}";
        $str = file_get_contents($url);
        if ($str)
            return json_decode($str, 1);
        return array('errcode' => '-2', 'errmsg' => '网络错误');
    }

    public function getUserInfo($access_token, $openid, $lang='zh_CN') {
        $url = "{$this->_weixin_userinfo_path}?access_token={$access_token}&openid={$openid}&lang={$lang}";
        $str = file_get_contents($url);
        if ($str['code'])
            return json_decode($str, 1);
        return array('errcode' => '-2', 'errmsg' => '网络错误');
    }
}