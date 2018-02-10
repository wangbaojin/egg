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

function send_sms($mobile,$message,$from='养鸡管家',$sprdid='1012818'){
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
function send_sms_code($mobile){
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
    if(send_sms($mobile,$message)){
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
    curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
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
function getEggcoinAccountId($eggcoin_account_address,$custom_name='')
{
    $m   = M('EggcoinAccount');
    $res = $m->where('account_address="'.$eggcoin_account_address.'"')->find();
    if($res) return $res;
    $add['account_address'] = $eggcoin_account_address;
    $add['custom_name']     = $custom_name;
    $add['created']         = date('Y-m-d H:i:s');
    $add['state']           = 1;
    $res = $m->add($add);
    return $res;
}

/*根据id获取钱包地址*/
function getEggcoinAccountInfo($eggcoin_account_id)
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
    if($m->where('user_id='.$user_id)->find()); return true;
    $add['user_id'] = $user_id;
    $add['expiry_time'] = $expiry_time;
    $add['created'] = time();
    return $m->add($add);
}

/*记录流水*/
function addRecord($data)
{
    $return_data['code'] = 0;

    $not_null_param  = array(
        'user_id'     => '手机号不能为空',
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
    $raise_record['reason_type']      = $data['reason_type'];//事由类型id：1.充值、2.饲料认购，3.饲料消耗；4.药物及其他支出；5.现金收益；6.饲料补扣；7.药物及其他支出补扣'
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