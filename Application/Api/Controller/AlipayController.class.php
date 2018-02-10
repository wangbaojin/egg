<?php
/**
 * Created by PhpStorm.
 * User: lkk
 * Date: 18/2/7
 * Time: 下午11:18
 */

namespace Api\Controller;

class AlipayController extends ApiController
{
   /*获得支付宝订单签名的接口--充值*/
    public function getAlipayInfo(){
     $notify_url = "http://api.lianyangji.io/EggcoinRecharge/rechargeNotifyUrl";
     $parter3 = "2088912508777882";
     $seller3 = "server@bkanbing.com";
    $data['order_sn'] = I('get.order_sn');
     $this->checkParam($data);
     $order_sn = $data['order_sn'];
    // $order = M('Order2')->where("order_sn = $order_sn")->find();
       /* if (!$order) {
            $this->api_error(5001, '暂无订单信息');
        }*/
     $order_info_array = array(

                    'partner' => $parter3,
                    'seller_id' => $seller3,
                    'out_trade_no' => $order_sn,
                    'subject' => "母鸡",
                    'body' => "母鸡",
                    'total_fee' =>"0.01",
                    'notify_url' => $notify_url,
                    'service' => "mobile.securitypay.pay",
                    'payment_type' => 1,
                    '_input_charset' => "utf-8",
                    'it_b_pay' => "30m",
                    'return_url' => "m.alipay.com",

                );

      $order_info_array = argSort($order_info_array);
      $order_info = '';
      foreach ($order_info_array as $key => $val) {
              if ($order_info == '') {
                     $order_info = $key . '=' . '"' . $val . '"';
                  } else {
                    $order_info = $order_info . '&' . $key . '=' . '"' . $val . '"';
                    }
                }
      $sign = rsaSign($order_info, "/usr/local/nginx/liannewkey/rsa_private_key.pem");
      $sign = urlencode($sign);

      $data['orderInfo'] = $order_info . '&sign=' . '"' . $sign . '"' . '&sign_type="RSA"';
      $data['sign'] = $sign;
      $this->api_return('返回支付宝签名成功', $data);

          
 }
    
}
