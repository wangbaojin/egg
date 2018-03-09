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
     //$notify_url = "http://api.lianyangji.io/EggcoinRecharge/rechargeNotifyUrl";
    $notify_url = "http://pay.kuailededan.com/notify_url3.php";
    $parter3 = "2088912508777882";
    $seller3 = "server@bkanbing.com";
    $data['order_sn'] = I('get.order_sn');
    $this->checkParam($data);
    $order_sn = $data['order_sn'];
    $order = M('recharge')->where("order_sn = $order_sn")->find();
    

    $order_info_array = array(

                    'partner' => $parter3,
                    'seller_id' => $seller3,
                    'out_trade_no' => $order_sn,
                    'subject' => "饲料充值",
                    'body' => "饲料充值",
                    'total_fee' =>order['amount'],
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
 /*获得支付宝订单签名的接口--买鸡*/
    public function getChickenAlipayInfo(){
     //$notify_url = "http://api.lianyangji.io/EggcoinRecharge/rechargeNotifyUrl";
    $notify_url = "http://pay.kuailededan.com/notify_url4.php";
    $parter3 = "2088912508777882";
    $seller3 = "server@bkanbing.com";
    $data['order_sn'] = I('get.order_sn');
    $this->checkParam($data);
    $order_sn = $data['order_sn'];
    $order = M('chicken_order')->where("order_sn = $order_sn")->find();
    // $map['id'] =  $order['chicken_type'];
    // $chicken = M('chicken_type')->where($map)->find();
    // $price = round($chicken['price'] - $chicken['discount'],2);
    $price = $order["total_price"];

     $order_info_array = array(

                    'partner' => $parter3,
                    'seller_id' => $seller3,
                    'out_trade_no' => $order_sn,
                    'subject' => "母鸡",
                    'body' => "母鸡",
                    'total_fee' =>$price,
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
