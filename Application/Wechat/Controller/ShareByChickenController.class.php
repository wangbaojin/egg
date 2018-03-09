<?php
namespace Wechat\Controller;
use Think\Controller;
use Com\Wechat\Wechat;
use Com\Wechat\Wechatjssdk;
use Com\Wechat\WechatPay;
use Com\Qrcode\Qr;

class ShareByChickenController extends PublicController
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

    // 分享首页
    /**
     *
     */
    public function index()
    {
        //echo "<pre>";
        //echo $this->_user_id;
        $m = M('User');
        $user_info = $m->where('state=1')->find($this->_user_id);
        if(!$user_info) die('该用户不存在');

        if($this->_wx_state['invite_code']) $invite_user_info = $m->where('invite_code="'.$this->_wx_state['invite_code'].'"')->find();

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

            // 邀请记录
            $invite_map['invite_user_id'] = $invite_user_info['id'];
            $invite_map['user_id']        = $user_info['id'];
            $invite_map['add_date']       = date('Y-m-d');
            $res = M('InviteBuy')->where($invite_map)->find();
            if(!M('InviteBuy')->where($invite_map)->find())
            {
                $invite_map['add_time']       = time();
                M('InviteBuy')->add($invite_map);
            }
        }
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $ticket = file_get_contents("http://wechat.jiagehao.cn/WeChat/getSignPackage?url=".urlencode($url));
        $this->assign('ticket',$ticket);
        $this->assign('invite_code',$this->_wx_state['invite_code']);
        $this->assign('self_invite_code',$user_info['invite_code']);
        $this->display();
    }

    public function weixinpay()
    {
        if(!$_GET['order_sn'] or !$order_sn=$_GET['order_sn']) die('订单号错误');

        $m = M('ChickenOrder');
        $order_info = $m->where('order_sn=' . $order_sn)->find();
        if (!$order_info) die('订单不存在');

        $user_info = getUserInfoByUserId($this->_user_id);
        if(!$user_info) die('用户信息获取失败');

        $pay = new WechatPay();
        $paydata = array(
            'body' => '链养鸡认养 X '.$order_info['num'].' 只',
            'out_trade_no' => $order_info['order_sn'],
            'total_fee' => $order_info['pay_price']*100,//分
            //'openid' => 'oCA61jkiwssJ36igRtgovvCg45bo',
            'openid' => $user_info['data']['wechart_info']['openid'],
            //'spbill_create_ip' => $this->getIp(),
            'spbill_create_ip' => $_SERVER["REMOTE_ADDR"],
            'notify_url' => 'http://wechat.jiagehao.cn/ShareByChicken/buyChickenNotifyUrl',
            'trade_type' => 'JSAPI',//JSAPI 公众号支付 NATIVE 扫码支付 APP APP支付
        );
        $res = $pay->unifiedOrder($paydata);
        $chick = $pay->checkSign($res);

        if($res['errcode'] == 200 and $pay->checkSign($res['data']))
        {
            $jssdkdata = array('appId'=>$res['data']['appid'],'timeStamp'=>time(),'nonceStr'=>$res['data']['nonce_str'],'package'=>'prepay_id='.$res['data']['prepay_id'],'signType'=>'MD5');
            $jssdkdata['paySign'] = $pay->getSign($jssdkdata);
            //$jssdkdata['package'] = $res['data']['prepay_id'];
            //print_r($jssdkdata);die;
            $result['data'] = $jssdkdata;
            $result['code'] = 1;
        }
        else
        {
            $result['code'] = 0;
            $result['msg']  = $res['errmsg'];
        }
        if($result['code']==0) die($res['errmsg']);
        $this->assign('jssdkdata',json_encode($jssdkdata));
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $ticket = file_get_contents("http://wechat.jiagehao.cn/WeChat/getSignPackage?url=".urlencode($url));
        $this->assign('ticket',$ticket);
        $this->display();
    }

    //获取用户IP地址
    public function getIp()
    {

        if(!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if(!empty($_SERVER["REMOTE_ADDR"]))
        {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        else
        {
            $cip = '';
        }
        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);

        return $cip;
    }

    /*
     *  获取认养鸡类型
     * */
    public function getChickenType()
    {
        $list = M('ChickenType')->where('state=1')->select();
        if (!$list) $this->api_error(20002, '暂无可认养的鸡');

        foreach ($list as $k => $v) {
            $list[$k]['discount_price'] = $v['discount'] ? $v['price'] - $v['discount'] : $v['price'];
        }
        $data['data'] = $list;
        $this->api_return('success', $data);
    }

    /*
     * 认养鸡
     */
    public function buyChicken()
    {
        $batch_info = getCurrentBatch();
        if (!$batch_info) $this->api_error(20002, 暂无可认养的鸡);

        $data['data'] = $batch_info;
        $data['data']['order_sn'] = order_no();

        // 可认购量
        $data['data']['buy_limit'] = 5;
        $this->api_return('success', $data);
    }

    /*
     * 确认认养
     * */
    public function confirmBuyChicken()
    {
        $not_null_param = array(
            'user_id' => '请先登录',
            'order_sn' => '哎呦!订单号错误',
            'chicken_batch' => '请选择批次',
            'chicken_type' => '选择个要认养的类型吧',
            'num' => '您要认购多少只鸡呢?是要买完吗?这么任性的吗?',
        );
        $data = I('post.');
        $data['user_id'] = $this->_user_id;
        $check_res = check_not_null_param($not_null_param, $data);
        if ($check_res) $this->api_error(20001, $check_res);
        if ($data['num']>5) $this->api_error(20001,'每次认购数量不可超过5只!');

        // 检查有没有未完成的订单,未完成之前让其先支付前一单(待定)

        $m = M('ChickenOrder');
        if ($res = $m->where('order_sn=' . $data['order_sn'])->find()) {
            //$this->api_error(20002, '订单号错误');
            $return_data['order_sn'] = $data['order_sn'];
            $this->api_return('success',$return_data);
        }

        $data['created'] = $data['updated'] = time();

        $trans = M();
        $trans->startTrans();

        // 类型判断
        $type_info = M('chicken_type')->where('state=1 && id=' . $data['chicken_type'])->find();

        if (!$type_info) $this->api_error(20004, '该类型不存在');

        $price = round($type_info['price'] - $type_info['discount'],2);
        if (!$price) $this->api_error(20004, '该类型母鸡已暂停认养');

        // 是否认购成功、主要判断改该批次是否还有剩余的鸡:
        // $bind_res['code']为1:则成功,0.则失败,失败原因为:$bind_res['msg']
        $bind_res = $this->lockChicken($data['user_id'], $data['chicken_batch'], $data['chicken_type'], $data['num']);

        // 鸡认购锁定成功则继续下单,否则告知已发行完
        if ($bind_res['code'] == 0) {
            $trans->rollback();
            $this->api_error(20005, $bind_res['msg']);
        }

        // 订单锁定时间为15分钟,鸡锁定时间为18分钟,多预留了3分钟的时间,请至少三分钟内完成订单记录
        // 支付回调后先依据待定锁定时间,再判断鸡锁定时间,不超时绑定成功后则此次交易成功
        // 下单,允许其有15分钟的支付时间,否则支付超时,认购的鸡也将被释放,鸡库存刷新(该逻辑在支付回调时处理)
        $order_info = array();
        $order_info['lock_time'] = time() + 900;
        $order_info['order_sn'] = $data['order_sn'];
        $order_info['user_id'] = $data['user_id'];
        $order_info['num'] = $data['num'];
        $order_info['chicken_batch'] = $data['chicken_batch'];
        $order_info['chicken_type'] = $data['chicken_type'];
        $order_info['created'] = $order_info['updated'] = time();
        $order_info['total_price'] = $order_info['pay_price'] = $price * $data['num'];
        //$order_info['total_price'] = $order_info['pay_price'] = 0.01;
        $order_info['pay_state'] = 1;
        $order_info['state'] = 1;

        $count = M('Chicken')->where('(state=4 or state=5) and user_id='.$data['user_id'])->count();
        $invite_user_id = M('User')->where('invite_code="'.$data['invite_code'].'"')->getField('id');
        if($data['invite_code'] and $invite_user_id and ($invite_user_id != $data['user_id']) and $count<1) {// 如果之前未购买,现在被邀请购买则记录

            // 订单记录邀请码
            $order_info['invite_code'] = $data['invite_code'];

            $invite_m = M('InviteBuy');
            $invite_map['invite_user_id'] = $invite_user_id;
            $invite_map['user_id'] = $order_info['user_id'];
            $invite_info = $invite_m->where($invite_map)->oder('add_time desc')->find();
            if(!$invite_info)
            {
                $invite_add['invite_user_id'] = $invite_user_id;
                $invite_add['user_id']   = $order_info['user_id'];
                $invite_add['add_date']  = date('Y-m-d');
                $invite_add['buy_state'] = 2;//状态：1.已购买；2.未购买
                $invite_add['buy_num']   = $order_info['num'];
                $invite_add['order_sn']  = $order_info['order_sn'];
                $invite_m->add($invite_map);
            }
            if($invite_info and $invite_info['buy_state']==2)
            {
                $invite_update['buy_num']   = $order_info['num'];
                $invite_update['order_sn']  = $order_info['order_sn'];
                $invite_m->where('id='.$invite_info['id'])->save($invite_update);
            }
        }

        if (!$m->add($order_info)) {
            $trans->rollback();
            $this->api_error(20006, '认养失败');
        }

        // 下单成功后,返回信息给客户端拉起支付
        $trans->commit();
        $return_data['order_sn'] = $order_info['order_sn'];
        $this->api_return('success',$return_data);
    }

    /*购买回调,此处错误应该做日志记录,调试先返回信息便于调试*/
    public function buyChickenNotifyUrl()
    {
        $get_data = file_get_contents("php://input");
        if(!$get_data) return;

        $pay = new WechatPay();
        $data = $pay->xmlToArray($get_data);
        if(!$data or !$data['out_trade_no']) return;

        // 订单号
        $order_sn = $data['out_trade_no'];

        // 记录订单日志
        $wxpay_log_m = M('WxpayLog');
        $wxpay_log_res = $wxpay_log_m->where('order_sn="'.$data['out_trade_no'].'"')->find();
        if($wxpay_log_res)
        {
            $wxpay_log_m->where('order_sn="'.$data['out_trade_no'].'"')->setField('info',json_encode($data));
        }
        else
        {
            $wxpay_log_add = array();
            $wxpay_log_add['order_sn'] = $data['out_trade_no'];
            $wxpay_log_add['info'] = json_encode($data);
            $wxpay_log_add['add_time'] = time();
            $wxpay_log_m->add($wxpay_log_add);
        }


        /*$str = '{"appid":"wxbde5adebd8d6924a","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"Y","mch_id":"1498957392","nonce_str":"L7xCxPAqzy3pLoW3","openid":"oCA61jkiwssJ36igRtgovvCg45bo","out_trade_no":"201803084424401941002052","result_code":"SUCCESS","return_code":"SUCCESS","sign":"E6C327EC860737AF3574E3CFF1B69DFB","time_end":"20180308013734","total_fee":"1","trade_type":"JSAPI","transaction_id":"4200000060201803084585993658"}';
        $data = json_decode($str,1);
        echo "<pre>";
        print_r($data);*/

        // 订单状态成功则处理
        if($data['result_code']!='SUCCESS') die($data['result_code']);

        // 验证签名
        if(!$pay->checkSign($data)) die('验签失败!');

        $c_m = M('Chicken');
        $m   = M('ChickenOrder');

        // 查看订单
        $order = $m->where('order_sn=' . $order_sn)->find();
        if (!$order) $this->api_error(20002, '订单不存在');
        if ( $order['pay_state'] != 1 ) $this->api_error(20002, '订单已处理');

        $saveData = array();
        $saveData['updated_at'] = time();

        // 订单超时
        if (time() > $order['lock_time'])
        {

            $saveData['state'] = 4;
            $saveData['pay_state'] = (($data['cash_fee']==$data['total_fee']) and ($data['total_fee']==$order['pay_price']*100)) ? 2 : 5;//支付状态：1.待支付，2.支付完成，3.退款中，4.已退款';5.支付金额异常
            $saveData['err_msg'] = 'ORDER_TIMEOUT';
            $change_res = $m->where('id=' . $order['id'])->save($saveData);
            //if (!$change_res) Log::record('订单支付超时状态修改失败,INFO:' . json_encode($saveData), 'BUY_ChICKEN', true);
            $this->api_error(20004, '订单超时');
        }

        // 修改订单状态
        $saveData['state'] = 3;
        $saveData['pay_state'] = (($data['cash_fee']==$data['total_fee']) and ($data['total_fee']==$order['pay_price']*100)) ? 2 : 5;//支付状态：1.待支付，2.支付完成，3.退款中，4.已退款';5.支付金额异常
        $change_res = $m->where('id=' . $order['id'])->save($saveData);
        if (!$change_res)
        {
            //Log::record('订单状态修改失败,INFO:' . json_encode($saveData), 'BUY_ChICKEN', true);
            $this->api_error(20005, '订单状态修改失败');
        }

        // 订单支付金额异常
        if($saveData['pay_state']==5) $this->api_error(20005, '订单支付金额异常');

        // 认购鸡,把下单锁定的鸡变为待绑定
        $trans = M();
        $trans->startTrans();
        $unlock_map = array();
        $unlock_map['user_id'] = $order['user_id'];
        $unlock_map['lock_time'] = array('GT', time());
        $unlock_map['state'] = 3;

        $lock_data['state'] = 4;// 状态：1.待认养，2.释放，3.锁定，4.待绑定;5.已认养
        $clock_res = $c_m->where($unlock_map)->limit($order['num'])->save($lock_data);
        if ($order['num'] != $clock_res)
        {
            $trans->rollback();
            //Log::record('认购鸡失败,INFO:' . json_encode($saveData), 'BUY_ChICKEN', true);
            $this->api_error(20006, '认购鸡失败');
        }

        // 查看有没有邀请购买、有邀请购买则奖励
        $invite_m = M('InviteBuy');
        $invite_map['order_sn'] = $order_sn;
        $invite_map['buy_state'] = 2;
        $invite_map['user_id']  = $order['user_id'];
        $invite_info = $invite_m->where($invite_map)->find();
        if($invite_info)
        {
            $change_invite_st = $invite_m->where('id='.$invite_info['id'])->setField('buy_state',1);
            if($change_invite_st)
            {
                // 发放奖励
                invite_success_reward($invite_info['invite_user_id'],$invite_info['id']);
            }
            else
            {
                // 记录邀请码无效
                //Log::record('邀请奖励状态修改失败,INFO:' . json_encode($order), 'buyChickenNotifyUrl', true);
            }
        }
        $trans->commit();
        $this->api_return('success');
    }

    /*锁定鸡*/
    private function lockChicken($user_id, $chicken_batch, $chicken_type, $num)
    {
        $data = array('code' => 0, 'msg' => '');
        if (!$user_id or !$chicken_batch or !$chicken_type or !$num) {
            $data['msg'] = '缺少参数';
            return $data;
        }

        // 查看该批次是否停止发行(待定)
        $m = M('Chicken');
        $lock_data['user_id'] = $user_id;
        $lock_data['chicken_type'] = $chicken_type;
        $lock_data['lock_time'] = time() + 1080;// 18分钟
        $lock_data['state'] = 3;// 状态：1.待认养，2.释放，3.锁定，4.待绑定;5.已认养

        $lock_map = array('user_id' => 0, 'state' => 1, 'chicken_batch' => $chicken_batch);
        $clock_res = $m->where($lock_map)->limit($num)->save($lock_data);

        if ($clock_res != $num) {
            $data['msg'] = '认购数量已不足';
            return $data;
        }
        $data['msg'] = 'success';
        $data['code'] = 1;
        return $data;
    }

    public function test()
    {
        $str = '4617,215.35,0,3131,6.7,2.3,33115,1,33271&
5687,269.25,2,3001,6.8,2.4,33111,2,33271&
6931,327.2,2,3029,6.9,2.3,0,1,33269&
8748,425.45,0,2953,7,2.3,33104,2,33267&
10521,505.55,33,3195,7.1,2.4,33099,1,33267&
12135,585.95,2,3280,7.2,2.3,0,2,33234&
19101,931.9,2,3199,7.3,2.3,33088,1,33232&
16002,785.2,2,3154,7.4,2.4,0,2,33230&
18373,917.6,5,3018,7.5,2.3,0,1,33228&
18968,963.8,0,3023,7.6,2.3,0,2,33223&
21163,1071.45,4,3039,7.7,2.4,0,1,33223&
21890,1128.25,2,3292,7.8,2.3,0,2,33219&
22773,1180.9,4,3330,7.9,2.3,0,1,33217&
24442,1262.65,4,3120,8,2.4,0,1,33213&
25362,1339.8,1,3195,8.1,2.3,0,1,33209&
25798,1353.6,5,3272,8.19999999999999,2.3,33099,1,33208&
25863,1369.35,6,3338,8.29999999999999,2.4,33093,1,33203&
27004,1427.4,4,3231,8.39999999999999,2.3,33088,1,33197&
27994,1506.85,3,3285,8.49999999999999,2.3,0,1,33193&
27050,1472.15,4,3315,8.4,2.4,0,1,33190&
29068,1584.6,4,3477,8.30000000000001,2.3,0,1,33186&
29321,1620.45,3,3310,8.20000000000002,2.3,0,1,33182&
26985,1489.05,2,3326,8.10000000000003,2.4,0,1,33179&
28790,1591.3,2,3326,8.00000000000004,2.3,0,2,33177&
29563,1619.5,4,3309,7.90000000000006,2.3,0,1,33175&
29030,1608.75,7,2170,7.80000000000007,2.4,0,2,33171&
29348,1638.15,1,3334,7.70000000000008,2.3,0,1,33164&
29893,1675.8,2,3418,7.60000000000009,2.3,0,2,33163&
29550,1662.85,2,3348,7.5000000000001,2.4,0,1,33161&
29547,1656.4,3,3390,7.40000000000011,2.3,0,2,33159&
29599,1658.8,5,3355,7.30000000000012,2.3,0,1,33156&
30129,1696.65,3,3422,7.20000000000013,2.4,0,2,33151&
29990,1709.45,5,3418,7.10000000000014,2.4,0,1,33148&
30416,1739.15,5,3467,7.00000000000016,2.3,0,2,33143&
28826,1654.45,1,3580,6.90000000000017,2.3,0,1,33138&
29895,1712.3,3,2712,6.80000000000018,2.4,0,1,33137&
30191,1739.55,3,3372,6.70000000000019,2.3,0,1,33134&
30084,1733.55,9,3299,6.6000000000002,2.3,0,1,33131&
30107,1735,4,3301,6.7,2.3,33115,1,33122&
30693,1755.65,3,3350,6.7999999999998,2.4,33111,1,33118&
30240,1742.9,4,3312,6.8999999999996,2.4,33107,1,33115&
30116,1738.05,4,3388,6.9999999999994,2.3,33104,1,33111&
30571,1766.55,3,3446,7.0999999999992,2.3,33099,1,33107&
29546,1711.95,5,3245,7.199999999999,2.4,33093,1,33104&
30042,1726.45,6,3290,7.2999999999988,2.3,33088,1,33099&
30752,1766.5,5,3314,7.3999999999986,2.4,0,0,33093&
29702,1715.75,2,3396,7.4999999999984,2.4,0,1,33088';
        $list_arr = explode('&',$str);
        echo "<pre>";
        $add_time = strtotime('2017-04-15');
        $day = 132;
        foreach ($list_arr as $k=>$v)
        {
            $tmp = array();
            $row_tmp = explode(',',trim(strip_tags($v)));
            foreach ($row_tmp as $kk=>$vv)
            {
                $row_tmp[$kk] = trim($vv);
            }
            $tmp['delivery_date'] = date('Y-m-d',$add_time);
            $today_price = M('TodayPrice')->where('delivery_date="'.$tmp['delivery_date'].'"')->find();
            $tmp['chicken_batch'] = 33;
            $tmp['lay_eggs'] = $row_tmp[0];
            $tmp['lay_eggs_weight'] = $row_tmp[1];
            $tmp['egg_price'] = $tmp['lay_eggs_weight']*$today_price['egg_price'];
            $tmp['death'] = $row_tmp[2];
            $tmp['feed_weight'] = $row_tmp[3];
            $tmp['feed_price'] = $tmp['feed_weight']*$today_price['feed_price'];
            $tmp['expenses'] = $row_tmp[6];
            $tmp['state'] = 2;
            $tmp['age_in_days'] = $day;
            $tmp['avg_eggcoin_income'] = $row_tmp[7];
            $new_arr[] = $tmp;

            $add_time += 86400;
            $day ++;
        }
        die;
        M('ChickenbatchTodayfeedDelivery')->addAll($new_arr);
        //print_r($new_arr);die;
        $sql = "INSET INTO eggcoin_today_price (delivery_date,egg_price,feed_price) VALUES ";
        foreach ($new_arr as $nk=>$nv)
        {
            $sql .= '("'.$nv[0].'",'.$nv[1].','.$nv[2].'),';
        }
        echo $sql;
    }
}