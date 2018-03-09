<?php
namespace Com\Wechat;
/**
 * Created by PhpStorm.
 * User: lkk
 * Date: 18/2/24
 * Time: 下午1:30
 */
class Wechatjssdk {

    private $appId = 'wxbde5adebd8d6924a';
    private $appSecret = '8f8823b8f1b45bcb609089ac946bec21';

    public function __construct($appId='', $appSecret='') {
        if($appId) $this->appId = $appId;
        if($appSecret) $this->appSecret = $appSecret;
    }

    /*获取jssdk所需签名*/
    public function getSignPackage($url='') {
        $jsapiTicket = $this->getJsApiTicket();
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $url ? $url : "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    /**/
    public function send_template_msg($tmp_ary)
    {
        $data = array('code'=>0,'msg'=>'');
        $not_null_param = array(
            'touser' => '接收者不可为空:touser',
            'template_id' => '模板ID不可为空:template_id',
            'data' => '模板数据不可为空:data',
        );
        $check_res = check_not_null_param($not_null_param, $tmp_ary);
        if ($check_res)
        {
            $data['msg']  = '参数错误!'.$check_res;
            return $data;
        }

        $accessToken = $this->getAccessToken();
        // 如果是企业号用以下URL获取access_token
        if($accessToken)
        {
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$accessToken";
            $res = $this->http_request($url,json_encode($tmp_ary));
            if($res && $res['errcode']==0)
            {
                $data['code'] = 1;
                $data['msg']  = 'SUCCESS!';
            }
            else
            {
                $data['msg']  = 'ERROR!'.$res['errmsg'];
            }
        }
        else
        {
            $data['msg']  = 'ACCESSTOKEN_ERROR!';
        }
        return $data;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket() {
        // jsapi_ticket 应该全局存储与更新
        $key = 'ticket_'.$this->appId;
        $ticket = S($key);
        if(!$ticket)
        {
            $accessToken = $this->getAccessToken();
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url),true);
            if($res['ticket'] && $ticket=$res['ticket']) S($key,$res['ticket'],array('type'=>'file','expire'=>7000));
        }
        return $ticket;
    }

    private function getAccessToken()
    {
        // access_token 应该全局存储与更新
        $key = 'access_token_'.$this->appId;
        $access_token = S($key);
        if(!$access_token)
        {
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url),true);
            if($res['access_token'] && $access_token=$res['access_token']) S($key,$res['access_token'],array('type'=>'file','expire'=>7000));
        }
        return $access_token;
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    //模拟 GET请求 及 POST请求
    public function http_request($url,$post=null) {
        #1.初始化curl
        $ch = curl_init();
        #2.设置请求地址
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //终端不验证curl
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //模拟 POST
        if(!empty($post)){
            curl_setopt($ch,CURLOPT_POST,1);//模拟post
            curl_setopt($ch,CURLOPT_POSTFIELDS,$post);//post内容
        }
        #3.执行curl
        $Token_Outopt = curl_exec($ch);
        #4.关闭curl
        curl_close($ch);
        #5.格式化数据
        $access_arr = json_decode($Token_Outopt,true);
        #6.返回值
        return $access_arr;
    }
}