<?php
/**
 * 全局公共函数.
 * User: sdf_sky
 * Date: 15/12/24
 * Time: 下午7:14
 */

/*生成一个24位唯一的订单号码*/
function order_no(){
    return date('Ymd').substr(time(),-5).substr(microtime(),2,5).'00'.rand(1000,9999);
}

function send_sms_post($data,$target){
    $url_info = parse_url($target);
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader .= "Host:" . $url_info['host'] . "\r\n";
    $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
    $httpheader .= "Content-Length:" . strlen($data) . "\r\n";
    $httpheader .= "Connection:close\r\n\r\n";
    //$httpheader .= "Connection:Keep-Alive\r\n\r\n";
    $httpheader .= $data;

    $fd = fsockopen($url_info['host'], 80);
    fwrite($fd, $httpheader);
    $gets = "";
    while(!feof($fd)) {
        $gets .= fread($fd, 128);
    }
    fclose($fd);
    if($gets != ''){
        $start = strpos($gets, '<?xml');
        if($start > 0) {
            $gets = substr($gets, $start);
        }
    }
    return $gets;
}

function send_sms($mobile,$message,$from='链养鸡',$sprdid='1012818'){
    $target = "http://cf.51welink.com/submitdata/Service.asmx/g_Submit";
    $post_data = "sname=dljiaruk&spwd=oc46WhbB&scorpid=&sprdid=".$sprdid."&sdst=".$mobile."&smsg=".rawurlencode($message."【".$from."】");
    $gets = send_sms_post($post_data, $target);
    $result = explode($gets,'');
    if($result[0] == 0){
        return true;
    }else{
        return false;
    }

}

/**
 * 发送验证码，调用send_sms_message()方法完成短信验证码发送功能
 * @param  [string] $mobile [手机号]
 * @return [int]
 */
function send_sms_code($mobile,$from='链养鸡'){
    if(mb_strlen($mobile) != 11){
        return 2;   //手机格式不正确
    }

    $code = rand(100000,999999);
    $message = $code.'是您本次的短信验证码，5分钟内有效';
    $data['message'] = $message;
    $data['mobile'] = $mobile;
    $data['created_at'] = time();
    $smsModel = M('Sms');
    $smsData = $smsModel->where(array('mobile'=>array('eq',$mobile)))->order('created_at desc')->limit(1)->find();
    if($smsData){
        //判断距离上次发送时间
        if(time() - $smsData['time'] < 60){
            $this->api_error('','两次短信发送间隔不能小于1分钟');
        }
    }
    if(send_sms($mobile,$message,$from)){
        $result = M('sms')->add($data);
        if($result){
            $return_data['code'] = $code;
            $return_data['send_time'] = $data['created_at'];
            return $return_data;   //短信发送成功
        }else{
            return 3;   //数据库添加数据失败
        }
    }else{
        return 4;   //短信发送失败
    }
}


/*获取当前完整地址*/
function getPresentCompletaUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    return "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}


/*curl 发起请求*/
function curl_request($url,$data='',$header=0){

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    //curl_setopt($ch,CURLOPT_TIMEOUT,10);

    //post
    if(!empty($data)) {
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    }
    //$header = array('Content-Type: application/json; charset=utf-8');
    curl_setopt($url, CURLOPT_HTTPHEADER,$header);
    //curl_setopt ($ch,CURLOPT_HEADER,0);
    $token_outopt = curl_exec($ch);
    curl_close($ch);
    return $token_outopt;
}

/**
 * 创建&提交FORM表单
 * @param string $url 需要提交到的地址
 * @param array $data 需要提交的数据
 * @return void
 * @author chunkuan <urcn@qq.com>
 */
function build_form($url, $data){
    $sHtml = "<!DOCTYPE html><html><head><title>Waiting...</title>";
    $sHtml.= "<meta http-equiv='content-type' content='text/html;charset=utf-8'></head>
	  <body><form id='lakalasubmit' enctype='application/json' name='lakalasubmit' action='".$url."' method='POST'>";
    foreach($data as $key => $value){
        $sHtml.= "<input type='hidden' name='".$key."' value='".$value."' style='width:90%;'/>";
    }
    $sHtml .= "</form>正在提交订单信息...";
    $sHtml .= "<script>document.forms['lakalasubmit'].submit();</script></body></html>";
    exit($sHtml);
}

/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param $para 需要拼接的数组
 * return 拼接完成以后的字符串
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

/*去除数组里面为空的字段*/
function removeEmptyKeys($data) {
    $res = array();
    if(!is_array($data)) {
        return $data;
    }
    while(list($k,$v)=each($data)) {
        if($v) {
            $res[$k] = $v;
        }
    }
    return $res;
}


/**
 * RSA签名
 * @param $data 待签名数据
 * @param $private_key_path 商户私钥文件路径
 * return 签名结果
 */
function rsaSign($data, $private_key_path) {
    $priKey = file_get_contents($private_key_path);
    $res = openssl_get_privatekey($priKey);
    openssl_sign($data, $sign, $res);
    openssl_free_key($res);
    //base64编码
    $sign = base64_encode($sign);
    return $sign;
}
/**
 * 对数组排序
 * @param $para 排序前的数组
 * return 排序后的数组
 */
function argSort($para) {
    ksort($para);
    reset($para);
    return $para;
}

//获取2点之间的距离
function GetDistance($lat1, $lng1, $lat2, $lng2)
{
    $radLat1 = $lng1 * (M_PI / 180);
    $radLat2 = $lng2 * (M_PI / 180);

    $a = $radLat1 - $radLat2;
    $b = ($lat1 * (M_PI / 180)) - ($lat2 * (M_PI / 180));

    $s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
    $s = $s * 6370996.81;
    $s = round($s * 10000) / 10000;
    return $s;
    //return 6370996.81 * acos(cos($lng1 * M_PI / 180) * cos($lng2 * M_PI / 180) * cos($lat1 * M_PI / 180 - $lat2 * M_PI / 180) + sin($lng1 * M_PI / 180) * sin($lng2 * M_PI / 180));
}

/*根据两地距离排序*/
function cmp($a, $b)
{
    return $a["distance"] > $b["distance"];
}

/*获取当前地址*/
function getSelf() {

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
    return $protocol.$_SERVER['SERVER_NAME'];
}

function check_not_null_param($not_null_param,$data)
{
    // 检查参数
    foreach ($not_null_param as $k=>$v)
    {
        if(!$data[$k]) return $v;
    }
}

/*获取钱包id,不存在则添加*/
function getEggcoinAccountId($eggcoin_account_address)
{
    $m   = M('EggcoinAccount');
    $res = $m->where('account_address="'.$eggcoin_account_address.'"')->getField('id');
    if(!$res) {
        $add['account_address'] = $eggcoin_account_address;
        $add['created']         = date('Y-m-d H:i:s');
        $add['state']           = 1;
        $res = $m->add($add);
    }
    return $res;
}

/*根据id获取钱包地址*/
function getEggcoinAccountInfoById($eggcoin_account_id)
{
    $m = M('EggcoinAccount');
    if($eggcoin_account_id)
    {
        return $m->where('id='.$eggcoin_account_id)->find();
    }
}

/*添加用户VIP信息*/
function addVip($user_id,$expiry_time)
{
    $m = M('UserVip');
    if($m->where('user_id='.$user_id)->find()) return true;
    $add['user_id'] = $user_id;
    $add['expiry_time'] = $expiry_time;
    $add['created'] = time();
    return $m->add($add);
}

/*记录饲养流水*/
function addRecord($data)
{
    $return_data['code'] = 0;

    $not_null_param  = array(
        'user_id' => '用户不能为空',
        'amount' => '请填写数量',
        'unit' => '请填写单位',
        'reason_type' => '请填写流水类型',
        'reason_narration' => '请填写流水标题',
        'state' => '请填写状态',
    );

    // 检查参数
    $check_res = check_not_null_param($not_null_param,$data);
    if($check_res)
    {
        $return_data['msg']  = $check_res;
        return $return_data;
    }
    $raise_record_m = M('ChickenRaiseRecord');
    $raise_record = array();
    $raise_record['user_id'] = $data['user_id'];
    $raise_record['amount']  = $data['amount'];
    $raise_record['unit']    = $data['unit'];
    if($data['reason_source_id']) $raise_record['reason_source_id'] = $data['reason_source_id'];
    if($data['chicken_id']) $raise_record['chicken_id'] = $data['chicken_id'];
    $raise_record['reason_type']      = $data['reason_type'];//事由类型id：1=>'充值',2=>'认购',3=>'投喂',4=>'支出',5=>'收益',6=>'饲料补扣',7=>'支出补扣',8=>'赠送',9=>'奖励',
    $raise_record['reason_narration'] = $data['reason_narration'];
    $raise_record['created_at'] = time();
    $raise_record['state'] = $data['state'] ? $data['state'] : 1; //状态：1.成功;2.失败;3.待处理
    $res =  $raise_record_m->add($raise_record);
    if(!$res)
    {
        $return_data['msg'] = '添加失败';
    }
    else
    {
        $return_data['code']= 1;
        $return_data['msg'] = 'success';
    }
    return $return_data;
}

function issueEggCoin($amount,$account_address)
{
    $return_data = array('code'=>0,'msg'=>'');
    if(!$amount or !$account_address)
    {
        $return_data['msg'] = '参数错误';
        return $return_data;
    }
    //$amount=1;
    //$account_address = '1KofcVX71Zpq5imj9Vw9hahFxu843NcTxY';
    //$account_address = '16xnbGyGqzDeD52gMjHunfa2psZdCJPvYW';
    $url  = "http://api.lianyangji.io:8080/HelloSpringMVC/eggcoin/sendEggcoin?amount=".$amount."&owner_account=".$account_address;

    $json = file_get_contents($url);
    $arr  = json_decode($json,true);
    if($arr && $arr['retcode']==0)
    {
        $return_data['code'] = 1;
        $return_data['msg']  = 'success';
    } else {
        $return_data['msg'] = $arr['retmsg'] ? $arr['retmsg'] : 'ERROR';
    }
    return $return_data;
}

/*记录数字发行流水*/
function addEggcoinRecord($data)
{
    $return_data['code'] = 0;

    $not_null_param  = array(
        'user_id' => '用户不能为空',
        //'eggcoin_account_id' => '钱包id不能为空',
        'amount' => '请填写数量',
        'reason_type' => '请填写流水类型',
        'reason_narration' => '请填写流水标题',
        'state' => '请填写状态',
    );

    // 检查参数
    $check_res = check_not_null_param($not_null_param,$data);
    if($check_res)
    {
        $return_data['msg']  = $check_res;
        return $return_data;
    }
    $raise_record_m = M('EggcoinRecord');
    $raise_record = array();
    $raise_record['user_id'] = $data['user_id'];
    if($data['eggcoin_account_id']) $raise_record['eggcoin_account_id'] = $data['eggcoin_account_id'];
    $raise_record['amount']  = $data['amount'];
    if($data['reason_source_id']) $raise_record['reason_source_id'] = $data['reason_source_id'];
    if($data['chicken_id']) $raise_record['chicken_id'] = $data['chicken_id'];
    if($data['err_code']) $raise_record['err_code'] = $data['err_code'];

    $raise_record['reason_type']      = $data['reason_type'];//事由类型id：1.收益；2.赠送；3.奖励'
    $raise_record['reason_narration'] = $data['reason_narration'];
    $raise_record['created_at'] = time();
    $raise_record['state'] = $data['state'] ? $data['state'] : 1; //状态：1.成功;2.失败;3.待处理
    $res =  $raise_record_m->add($raise_record);
    if(!$res)
    {
        $return_data['msg'] = '添加失败';
    }
    else
    {
        $return_data['code']= 1;
        $return_data['msg'] = 'success';
    }
    return $return_data;
}

/*递归创建目录*/
function directory( $dir )
{
    is_dir ( $dir )  or  (Directory(dirname( $dir ))  and   mkdir ( $dir , 0777));
}



// 根据手机号添加用户
function addUserByMobile($mobile)
{
    $re_data = array('code'=>1,'msg'=>'success');

    if(!$mobile)
    {
        $re_data['code'] = 20001;
        $re_data['msg']  = '手机号不可为空';
        return $re_data;
    }

    // 查询手机是否存在
    $m    = M('User');
    $info = $m->where('mobile='.$mobile)->find();
    if(!$info)
    {
        $user_data['mobile']  = $mobile;
        $user_data['user_st'] = 1;
        $user_data['created_at'] = $user_data['updated_at'] = time();
        $re_data['data']['user_id'] = $user_id = $m->add($user_data);
        if(!$user_id)
        {
            $re_data['code'] = 20002;
            $re_data['msg']  = '注册失败';
        }
    }
    else
    {
        $re_data['code'] = 20003;
        $re_data['msg']  = '手机号码已存在';
    }

    // 新用户操作
    if($user_id) newUserAction($user_id);
    return $re_data;
}

// 根据微信号添加用户
function addUserByWechat($wechat_info)
{
    $re_data = array('code'=>1,'msg'=>'success');

    // 查询微信账号是否存在
    $m        = M('UserWechatinfo');
    //$w_info   = $m->where('wx_open_id="'.$wechat_info['wx_open_id'].'"')->find();
    $w_u_info = $m->where('unionid="'.$wechat_info['unionid'].'"')->find();

    if($w_u_info)
    {
        // 更新信息
        $m->where('unionid="'.$wechat_info['unionid'].'"')->save($wechat_info);

        $re_data['code'] = 20003;
        $re_data['msg']  = '该微信已被绑定';
        $re_data['data']['user_id']  = $w_u_info['user_id'];
        return $re_data;
    }

    $trans = M();
    $trans->startTrans();

    // 注册一个账号
    $user_data['user_st'] = 1;
    $user_data['created_at'] = $user_data['updated_at'] = time();
    $re_data['data']['user_id'] = $user_id = M('User')->add($user_data);
    if(!$user_id) {
        $re_data['code'] = 20002;
        $re_data['msg']  = '注册失败';
        return $re_data;
    }

    // 新用户操作
    if($user_id) newUserAction($user_id);

    // 绑定微信
    $res = bindWeChat($user_id,$wechat_info);
    if($res['code']!=1)
    {
        $trans->rollback();
        $re_data['code'] = 20002;
        $re_data['msg']  = '注册失败!'.$res['msg'];
    }
    $trans->commit();
    return $re_data;
}

// 新用户操作
function newUserAction($user_id)
{
    //echo $user_id;die;
    // 添加vip
    if($user_id <= 10000) addVip($user_id,time()+85400*365*10);

    // 生成钱包
    $wallet_m = M('Wallet');
    $wallet_map['user_id']     = $user_id;
    $wallet_info               = $wallet_m->where($wallet_map)->find();
    if(!$wallet_info)
    {
        // 给初次用户赠送300g饲料
        $wallet_map['feed_amount'] = 0.3;
        $wallet_m->add($wallet_map);

        // 添加赠送饲料流水
        $record = array();
        $record['user_id'] = $user_id;
        $record['amount']  = $wallet_map['feed_amount']*1000;
        $record['reason_source_id'] = $user_id;
        $record['reason_type'] = 8;
        $record['reason_narration'] = 'VIP内测用户奖励';
        $record['state'] = 1;
        $record['unit'] = 'g';
        $record = addRecord($record);
        if(!$record) Log::record('用户赠送300g饲料流水记录失败,INFO:'.json_encode($record),'ADD_RECORD',true);
    }

    // 检查邀请码
    M('User')->where('invite_code = "" and id='.$user_id)->setField('invite_code',strtoupper(substr(md5($user_id.'teemo'),8,16)));
}

// 绑定手机
function bindMobile($user_id,$mobile)
{
    $re_data = array('code'=>1,'msg'=>'success');

    if(!$mobile)
    {
        $re_data['code'] = 20001;
        $re_data['msg']  = '手机号不可为空';
        return $re_data;
    }

    // 查询手机是否存在
    $m    = M('User');
    $info = $m->where('mobile='.$mobile)->find();
    if(!$info)
    {
        if(!$m->where('id='.$user_id)->setField('mobile',$mobile))
        {
            $re_data['code'] = 20002;
            $re_data['msg']  = '绑定失败';
        }
    }
    else
    {
        $re_data['code'] = 20003;
        $re_data['msg']  = '手机号码已存在';
    }
    return $re_data;
}

// 绑定微信
function bindWeChat($user_id,$wechat_info)
{
    $re_data = array('code'=>1,'msg'=>'success');

    $arr = array(

    );
    $not_null_param = array(
        //'wx_open_id'   => '获取微信open_id失败',
        'unionid'   => '获取微信信息失败',
        //'wx_pic'       => '缺少微信用户头像',
        //'wx_nick_name' => '缺少微信用户昵称',
    );

    // 检查参数
    $check_res = check_not_null_param($not_null_param,$wechat_info);
    if($check_res) {
        $re_data['code'] = 20001;
        $re_data['msg']  = $check_res;
        return $re_data;
    }

    // 查询微信账号是否存在
    $m        = M('UserWechatinfo');
    //$w_info   = $m->where('wx_open_id="'.$wechat_info['wx_open_id'].'"')->find();
    $w_u_info = $m->where('unionid="'.$wechat_info['unionid'].'"')->find();

    //if(!$w_info and !$w_u_info)
    if(!$w_u_info)
    {
        $wechat_info['user_id'] = $user_id;
        $wechat_info['created_at'] = time();
        $res = $m->add($wechat_info);
        if(!$res)
        {
            $re_data['code'] = 20002;
            $re_data['msg']  = '绑定失败';
        }
    }
    else
    {
        $re_data['code'] = 20003;
        $re_data['msg']  = '该微信已被绑定';
    }
    return $re_data;
}

// 根据用户id获取用户信息
function getUserInfoByUserId($user_id)
{
    $re_data = array('code'=>1,'msg'=>'success');
    $m = M('User');
    if(!$user_id)
    {
        $re_data['code'] = 20001;
        $re_data['msg']  = '缺少参数';
        return $re_data;
    }

    $map['user_id'] = $m_map['id'] = $user_id;

    $info = $m->where($m_map)->field('code,send_time,trade_pass_wd',true)->find();
    if(!$info)
    {
        $re_data['code'] = 20002;
        $re_data['msg']  = '用户不存在';
        return $re_data;
    }

    // 邮箱状态
    if(!$info['email']) $info['email_status'] = 3;
    $info['email_status_info'] = getEmailStatusInfo($info['email_status']);

    // 微信信息
    $wechart_info = M('UserWechatinfo')->field('user_id,created_at',true)->where($map)->find();
    $info['wechart_info'] = $wechart_info ? $wechart_info : array();
    if($info['wechart_info'])
    {
        $info['wechart_info']['wx_nick_name'] = base64_decode($info['wechart_info']['wx_nick_name']);
        $info['wechart_info']['sex_info'] = getWechatUserSexInfo($info['wechart_info']['sex']);
    }

    // vip
    $vip = M('UserVip')->where($map)->find();
    $info['vip'] = $vip ? 1 : 2;
    $info['vip_info'] = $info['vip']==1 ? '内测会员' : '普通会员';

    // 昵称
    if(!$info['full_name']) $info['full_name'] = $info['wechart_info']['wx_nick_name'];
    if(!$info['pic']) $info['pic'] = $info['wechart_info']['wx_pic'];

    // 注册时间
    $info['created_date'] = date('Y-m-d',$info['created_at']);
    $info['updated_date'] = date('Y-m-d',$info['updated_at']);

    // 微博
    $info['weibo_info'] = array();
    $re_data['data'] = $info;
    return $re_data;
}

/*微信性别*/
function getWechatUserSexInfo($id)
{
    if(!$id) return '未知';
    $info = array(
        1 => '🚹男士',
        2 => '🚺女士',
        3 => '未知',
    );
    return $info[$id];
}

/*邮箱状态*/
function getEmailStatusInfo($status)
{
    if(!$status) return '未设置邮箱';
    $status_info = array(
        1 => '已验证通过',
        2 => '待验证',
        3 => '未设置邮箱',
    );
    return $status_info[$status];
}

/*任务奖励*/
function task_reward($task='')
{
    $reward = array(
        'invite_reward' => 1,
        'sign_reward' => 10,
        'share_reward' => 20,
        'friend_login_reward' => 30,
        'transfer_success_reward' => 40
    );
    if(!$task) return $reward;
    return $reward[$task];
}

/*邀请购买成功奖励*/
function invite_success_reward($user_id,$invite_id='')
{
    // 数字发行
    $eggcoin_data = array();
    $eggcoin_data['user_id'] = $user_id;
    $eggcoin_data['amount'] = task_reward('invite_reward');
    $eggcoin_data['reason_type'] = 3;//事由类型id：1.收益；2.赠送；3.奖励'
    $eggcoin_data['reason_narration'] = '邀请购买';//事由名称
    if($invite_id) $eggcoin_data['reason_source_id'] = $invite_id;

    // 钱包地址
    $chicken_map = array();
    $chicken_map['state']   = array('in',array(4,5));
    $chicken_map['user_id'] = $user_id;
    $chicken_info           = M('Chicken')->where($chicken_map)->order('created desc')->find();

    if($chicken_info) $eggcoin_account_id = $chicken_info['eggcoin_account_id'];

    if($eggcoin_account_id) $eggcoin_account = getEggcoinAccountInfoById($eggcoin_account_id);

    if(!$eggcoin_account_id or !$eggcoin_account or !$eggcoin_account['account_address'])
    {
        $eggcoin_data['state']    = 3;//状态：1.成功;2.失败;3.待处理'
        $eggcoin_data['err_code'] = 'ADDRESS_NULL';
    }
    else
    {
        // 发放币
        $issueEggCoin_res = issueEggCoin((int)$eggcoin_data['amount'], $eggcoin_account['account_address']);
        //send_sms(18510249173,json_encode($issueEggCoin_res));
        if( $issueEggCoin_res['code'] == 1 )
        {
            $eggcoin_data['state'] = 1;//状态：1.成功;2.失败;3.待处理'
        }
        else
        {
            $eggcoin_data['state'] = 3;//状态：1.成功;2.失败;3.待处理'
            $eggcoin_data['err_code'] = 'ISSUE_ERROR';
        }
        $chicken_info['chicken_id']   = $chicken_info['id'];
        $eggcoin_data['eggcoin_account_id'] = $eggcoin_account_id;
    }

    $eggcoin_record = addEggcoinRecord($eggcoin_data);
    if($eggcoin_record['code']==0)
    {
        //Log::record('数字发行记录失败,INFO:' . json_encode($eggcoin_data), 'invite_success_reward', true);
    }
    return $eggcoin_record;
}

/*分享成功奖励*/


/*邀请好友登录成功奖励*/

/*
     *  获取当前发行批次鸡
     * */
function getCurrentBatch()
{
    $m = M('ChickenBatch');
    $map = array();
    $map['state'] = 1;
    $map['is_default'] = 1;

    // 发行时间
    //$map['start_time'] = array('lt',time());
    //$map['end_time']   = array('gt',time());
    return $m->where($map)->find();
}

function reissue()
{
    return;
    $c_m   = M('Chicken');
    $e_r_m = M('EggcoinRecord');
    $map = array();
    //echo "<pre>";
    // 找出3点半之前发放的币;
    $recordList = $e_r_m->where("created_at <= 1521012480")->select();
    //$recordList = $e_r_m->where("chicken_id is null")->select();
    // REISSUE reissue
    foreach ($recordList as $rk=>$rv)
    {
        // 钱包地址
        $chicken_map = array();
        $chicken_map['state']   = array('in',array(4,5));
        $chicken_map['id']      = $rv['chicken_id'];
        $chicken_map['user_id'] = $rv['user_id'];
        $chicken_info = $c_m->where($chicken_map)->find();
        if($chicken_info && $chicken_info['eggcoin_account_id'] && ($chicken_info['eggcoin_account_id']!=$rv['eggcoin_account_id']) && ($rv['err_code'] != 'REISSUE_SUCCESS'))
        {
            // 钱包地址
            $eggcoin_account = getEggcoinAccountInfoById($chicken_info['eggcoin_account_id']);
            if($eggcoin_account and $eggcoin_account['account_address'])
            {
                $issueEggCoin_res = issueEggCoin((int)$rv['amount'],$eggcoin_account['account_address']);
                $update_data = array();
                $update_data['state']    = 1;//状态：1.成功;2.失败;3.待处理'
                if($issueEggCoin_res['code']==1)
                {

                    $update_data['err_code'] = 'REISSUE_SUCCESS';
                }
                else
                {
                    $update_data['err_code'] = 'REISSUE_ERROR';
                }

                // 如果补发成功把发放地址修改回来
                if($update_data['err_code']=='REISSUE_SUCCESS') $update_data['eggcoin_account_id'] = $chicken_info['eggcoin_account_id'];
                $res = $e_r_m->where('id='.$rv['id'])->save($update_data);
                //echo $res,'<br>';
            }
        }
        //if($chicken_info) $e_r_m->where('id='.$rv['id'])->setField('chicken_id',$chicken_info['id']);
    }
}