<?php
namespace Com\Wechat;
/*
 *WeixinPayClass 微信支付类
 *用于生成微信预订单及微信支付
 *@author    teemo君
 *@mail    1301582878@qq.com
 *@blog      www.dtuzi.com
 *@version      2.0
 *@date     2016-01-27
 *@last_date     2016-01-29
 */

class WechatPay{

    private $_result = '';
    private $_appid = 'wxbde5adebd8d6924a';				//微信分配的公众账号
    private $_mac_id = '1498957392';			//微信支付分配的商户号
    private $_device_info = '';		//终端设备号
    private $_pay_unifiedorder_path = "https://api.mch.weixin.qq.com/pay/unifiedorder";//统一下单接口
    private $_needParam = array(//必填字段
        "appid","mch_id","nonce_str","sign","body","out_trade_no","total_fee","spbill_create_ip","notify_url","trade_type"
    );
    private $_trade_type = array(//交易类型
        "JSAPI",	//公众号支付
        "NATIVE",	//原生码支付
        "APP",		//APP支付
        "MWEB",		//H5支付
    );
    private $_key = '85ce0d6a319c09b564e2bd32cc64e38f';

    public function __construct() {

    }

    /*
     *把数组拼装成XML格式
     */
    function createXml($arr){

        // "Create" the document.
        $xml = new \DOMDocument( "1.0", "UTF-8" );

        // Create some elements.
        $xml_album = $xml->createElement( "xml" );

        foreach($arr as $k=>$v){
            $body = $xml->createElement($k,urlencode($v));
            $xml_album->appendChild($body);
        }

        $xml->appendChild( $xml_album );

        // Parse the XML.
        return $xml->saveXML();
    }

    /*
     *用"="将数组拼装成字符串
     */
    function createLinkstring($para) {
        $arg  = "";
        while (list ($key, $val) = each ($para)) {
            $arg.=$key."=".$val."&";
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,count($arg)-2);

        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

        return $arg;
    }

    /*
     *POST请求资源
     */
    function getHttpResponsePOST($url,$post=null) {
        $post = is_array($post) ? http_build_query($post) : $post;
        #1.初始化curl
        $ch = curl_init();
        #2.设置请求地址
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //终端不验证curl
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $this_header = array(
            "Content-Type:application/html;charset=UTF-8"
        );
        curl_setopt($ch,CURLOPT_HTTPHEADER,$this_header);
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
        //$access_arr = json_decode($Token_Outopt,true);

        #6.返回值
        return $Token_Outopt;
    }

    /**
     * 	作用：将xml转为array
     */
    public function xmlToArray($xml)
    {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    /*
     *checkSign 对微信回调传回的内容进行验签
     *param $data 微信回调传回的内容
     *param $key  微信商户平台密钥
     *return true or false
     */
    public function checkSign($data) {

        $n_sign = $data['sign']; unset($data['sign']);
        ksort($data);
        $str = $this->createLinkstring($data);
        $stringSignTemp = $str."&key=".$this->_key;
        $sign = strtoupper(MD5($stringSignTemp));//获取签名
        if($sign==$n_sign) {
            return true;
        }
    }

    /*
     *getSign 根据内容获取签名
     *param $data 传递的内容
     *param $key  微信商户平台密钥
     *return string $sign 签名
     */
    public function getSign($data) {

        //获取签名
        ksort($data);
        $str = $this->createLinkstring($data);
        $stringSignTemp = $str."&key=".$this->_key;
        $sign = strtoupper(MD5($stringSignTemp));
        return $sign;
    }

    /*
     *微信支付
     */
    public function unifiedOrder($paydata) {

        $paydata['appid']  = $paydata['appid'] ? $paydata['appid'] : $this->_appid;
        $paydata['mch_id'] = $paydata['mch_id'] ? $paydata['mch_id'] : $this->_mac_id;
        $paydata['nonce_str'] = $this->createNonceStr();
        $paydata['sign'] = $this->getSign($paydata);
        foreach($this->_needParam as $v){

            if(empty($paydata[$v])) {
                $result['errcode'] = '301';
                $result['errmsg'] = $v.'字段不可为空';
                return $result;die;
            }
            if($v=='trade_type' && !in_array($paydata[$v],$this->_trade_type)) {
                $result['errcode'] = '301';
                $result['errmsg'] = $v.'交易类型错误';
                return $result;die;
            }
        }

        //生成xml
        ksort($paydata);
        $xml = urldecode($this->createXml($paydata));
        //echo $fileType = mb_detect_encoding($xml , array('UTF-8','GBK','LATIN1','BIG5')) ;die;
        //echo mb_detect_encoding(urldecode($xml),array('UTF-8','GBK','LATIN1','BIG5'));die;
        $this->_pay_unifiedorder_path;
        //发送支付请求
        $res = $this->getHttpResponsePOST($this->_pay_unifiedorder_path,$xml);
        if($res) {

            $data = $this->xmlToArray($res);
            if($data['return_code']=='SUCCESS') {

                $result['errcode'] = '200';
                $result['errmsg'] = 'ok';
                $result['data'] = $data;
            } else if($data['return_code']=='ORDERPAID') {

                $result['errcode'] = '308';
                $result['errmsg'] = '商户订单已支付';
            } else if($data['return_code']=='ORDERCLOSED') {

                $result['errcode'] = '309';
                $result['errmsg'] = '订单已关闭';
            } else {

                $result['errcode'] = '307';
                $result['errmsg'] = '订单错误';
                $result['data'] = $data;
            }
        } else {

            $result['errcode'] = '401';
            $result['errmsg'] = '请求失败';
        }
        return $result;die;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}
?>