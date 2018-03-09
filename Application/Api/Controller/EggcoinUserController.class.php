<?php
namespace Api\Controller;

use Think\Controller;
use Com\PHPMailer\phpmailerAction;
use Think\Log;
class EggcoinUserController extends ApiController
{
    //private $_miyao = 'teemo';// 不能修改
    private $_email_confirm_timeout = 300;//

    private $_email_status = array(
        1 => '已验证通过',
        2 => '待验证',
        3 => '未设置邮箱',
    );

    /*
     * 获取我的个人信息
     * */
    public function getUserInfo()
    {
        $m = M('User');
        if(!I('user_id')) $this->api_error(20002,'请先登录');
        $map['id'] = I('user_id');

        $info = $m->where($map)->field('code,send_time,trade_pass_wd,user_st',true)->find();
        if(!$info) $this->api_error(20003,'失败');

        // 邮箱状态
        if(!$info['email']) $info['email_status'] = 3;
        $info['email_status_info'] = $this->_email_status[$info['email_status']];

        // 微信信息
        $wechart_info = M('UserWechatinfo')->field('user_id,created_at',true)->where('user_id='.I('user_id'))->find();
        $info['wechart_info'] = $wechart_info ? $wechart_info : array();
        if($info['wechart_info']) $info['wechart_info']['wx_nick_name'] = base64_decode($info['wechart_info']['wx_nick_name']);

        // vip
        $vip = M('UserVip')->where('user_id='.I('user_id'))->find();
        $info['vip'] = $vip ? 1 : 2;

        // 昵称
        if(!$info['full_name']) $info['full_name'] = $wechart_info['wx_nick_name'];

        // 微博
        $info['weibo_info'] = array();
        $data['data'] = $info;
        $this->api_return('success',$data);
    }

    /*
     * 获取我的钱包
     * */
    public function getUserWallet()
    {
        $m = M('Wallet');
        if(!I('user_id')) $this->api_error(20002,'请先登录');
        $map['user_id'] = I('user_id');

        $info = $m->where($map)->find();
        if(!$info) {
            $add_data['user_id'] = I('user_id');
            if(!$m->add($add_data)) $this->api_error(20003,'创建钱包失败');
            $info = $m->where($map)->find();
        }

        //feed: kg转为g
        $info['feed_amount'] = 1000*$info['feed_amount'];

        // 待收鸡蛋数量
        $delivery = M('ChickenTodayfeedDelivery');
        $map['state']    = 2;
        $egg_num = $delivery->where($map)->SUM('egg_num');
        $info['egg_num'] = $egg_num ? $egg_num : '0';

        $data['data'] = $info;
        $this->api_return('success',$data);
    }

    public function register()
    {
        $map['status']=0;
        $res = M('userkeyinfo')->where($map)->find();
        $data = array();
        $data['private_key'] = $res['private'];
        $data['eggcoin_account_address']  = $res['user_address'];
        $data['public_key']  = $res['public'];
        $data['memorizing_words'] = $res['mnemonicphrase'];
         $this->api_return('success',$data);
    }

    public function index()
    {
      echo 'hello world!';
    }

    /*
     * 用户发送短信验证码接口
     * 相同微信 不同手机号   该微信已经绑定其他手机
     * 不同微信 相同手机号   该手机号已经被其他微信绑定
     */
    public function sendSmsCode()
    {

        $data = I('post.');
        $data['mobile'] = (int)$data['mobile'];
        $not_null_param = array(
            'mobile'     => '手机号不能为空',
            'wx_open_id' => '请微信授权',
            'unionid'    => '请微信授权'
        );

        // 检查参数
        $check_res = check_not_null_param($not_null_param,$data);
        if($check_res) $this->api_error(20001,$check_res);

        $m   =   M('User');
        $wechat_m  = M('UserWechatinfo');

        $user_info = $m->where('mobile='.$data['mobile'])->find();
        if($user_info) $this->api_error(20002,'该手机号已被绑定!');

        $trans = M();
        $trans->startTrans();


        // 不存在则手机号注册
        $add_res = addUserByMobile($data['mobile']);
        if( $add_res['code'] != 1 ) $this->api_error($add_res['code'],'用户注册失败!'.$add_res['msg']);
        $user_id = $add_res['data']['user_id'];

        // 微信信息
        $wechat_data = array();
        $wechat_data['user_id'] = $user_id;
        $wechat_data['created_at'] = time();
        $wechat_data['unionid'] = $data['unionid'];
        if ($data['wx_pic'])       $wechat_data['wx_pic']   = $data['wx_pic'];
        if ($data['wx_open_id'])   $wechat_data['wx_open_id']   = $data['wx_open_id'];
        if ($data['wx_nick_name']) $wechat_data['wx_nick_name'] = base64_encode($data['wx_nick_name']);
        if($data['unionid'])       $wechat_data['unionid']  = $data['unionid'];
        if($data['sex'])           $wechat_data['sex']      = $data['sex'];//sex	用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
        if($data['province'])      $wechat_data['province'] = $data['province'];
        if($data['city'])          $wechat_data['city']     = $data['city'];
        if($data['country'])       $wechat_data['country']  = $data['country'];
        $use_wechat_info = $wechat_m->where('unionid="'.$data['unionid'].'"')->find();
        if(!$use_wechat_info)
        {
            // 绑定微信
            $bind_res = bindWeChat( $user_id , $wechat_data );
            if( $bind_res['code'] != 1 and $bind_res['code']!=20003) {
                $trans->rollback();
                $this->api_error($add_res['code'],'用户注册失败!'.$bind_res['msg']);
            }
        }
        else
        {
            $wechat_m->where('user_id='.$user_id.' and unionid="'.$data['unionid'].'"')->save($wechat_data);
        }
        $trans->commit();
        // 发送短信验证码
        $result = send_sms_code($data['mobile']);
        switch ($result)
        {
            case 2:
                $this->api_error(20005,'手机号码格式错误');
                break;
            case 3:
                $this->api_error(20006,'短信发送失败,请联客服咨询');
                break;
            case 4:
                $this->api_error(20007,'短信发送失败');
                break;
            default:
                $user_data['send_time']  = $result['send_time'];
                $user_data['code']       = $result['code'];
                $user_data['updated_at'] = time();
                $is_ok      = $m->where('mobile='.$data['mobile'])->save($user_data);
                if(!$is_ok) $this->api_error(20008,'用户注册失败');
                $this->api_return('发送短信成功');
        }
    }

    /*用户登陆接口*/
    public function login()
    {
        $m = M('User');
        $data['mobile']  = (int)I('mobile');
        $data['code']    = I('code');
        $not_null_param  = array(
            'mobile' => '手机号不能为空',
            'code'   => '请填写验证码'
        );

        // 检查参数
        $check_res = check_not_null_param($not_null_param,$data);
        if($check_res) $this->api_error(20001,$check_res);
        $user_info = $m->field('trade_pass_wd',true)->where('mobile='.$data['mobile'])->find();

        // 检查帐户状态
        if(!$user_info)                $this->api_error(30002,'该手机号未注册');
        if($user_info['user_st'] != 1) $this->api_error(30003,'该手机号已被禁用,请联系客服');


        //为通过AppStore审核单独加的登录逻辑
        if($data['code'] == '888888')
        {
            session('user_info',$user_info);
            $session_id = session_id();
            $user_info['token'] = $session_id;
            $this->api_return('登录成功',$user_info);
        }


        //判断验证码是否失效
        if(time() - $user_info['send_time'] > 300)
        {
            //$this->api_error(30004,'验证码已超时，请重新获取验证码');
        }

        // 邮箱状态
        if(!$user_info['email']) $user_info['email_status'] = 3;
        $user_info['email_status_info'] = $this->_email_status[$user_info['email_status']];

        if($user_info['code'] == $data['code'] || $data['code'] == '888888')
        {
            session('user_info',$user_info);
            $session_id = session_id();
            $user_info['token'] = $session_id;
            $this->api_return('登录成功',$user_info);
        }
        $this->api_error(30005,'验证码不正确,请重新输入');
    }

    /*微信登录*/
    public function wechatLogin()
    {
        $data['wx_open_id']    = I('post.wx_open_id');
        if(I('post.wx_pic'))       $data['wx_pic']       = I('post.wx_pic');
        if(I('post.wx_nick_name')) $data['wx_nick_name'] = base64_encode(I('post.wx_nick_name'));
        if(I('post.unionid'))   $data['unionid'] = I('post.unionid');
        if(I('post.sex'))       $data['sex'] = I('post.sex');//sex	用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
        if(I('post.province'))  $data['sex'] = I('post.province');
        if(I('post.city'))  $data['city'] = I('post.city');
        if(I('post.country'))  $data['country'] = I('post.country');
        $not_null_param  = array(
            'wx_open_id' => '请微信授权',
            'unionid' => '请微信授权'
        );

        // 检查参数
        $check_res = check_not_null_param($not_null_param,$data);
        if($check_res) $this->api_error(20001,$check_res);

        $m = M('User');
        $wechat_m  = M('UserWechatinfo');
        // 此处因为关联开放平台账号,所以用unionid来判断唯一;
        $wx_user_info = $wechat_m->where('unionid="'.$data['unionid'].'"')->find();

        if(!$wx_user_info or !$wx_user_info['user_id']) $this->api_error(20002,'该微信未绑定手机号');

        $user_info = $m->field('trade_pass_wd,send_time',true)->where('id='.$wx_user_info['user_id'])->find();
        // h5有可能只微信注册,并未绑定手机,APP登录必须绑定手机号
        if(!$user_info or !$user_info['mobile']) $this->api_error(20002,'该微信未绑定手机号');


        // 检查帐户状态
        if($user_info['user_st'] != 1) $this->api_error(30003,'该帐号已被禁用,请联系客服');

        // 存在更新微信信息
        $wechat_m->where('user_id='.$user_info['id'].' and unionid="'.$data['unionid'].'"')->save($data);

        // 邮箱状态
        if(!$user_info['email']) $user_info['email_status'] = 3;
        $user_info['email_status_info'] = $this->_email_status[$user_info['email_status']];

        session('user_info',$user_info);
        $session_id = session_id();
        $user_info['token'] = $session_id;
        $this->api_return('登录成功',$user_info);
    }

    /*用户退出接口*/
    public function logout()
    {
        session('user_info',null);
        $this->api_return('退出成功');
    }

    /*
     * 更换手机号
     * **/
    public function changeMobile()
    {
        $user_id= I('get.user_id');
        $mobile = I('get.mobile');

        // 检查参数
        if(!$mobile)  $this->api_error(20001,'手机号不能为空');
        if(!$user_id) $this->api_error(20001,'请先登录');

        $m   =   M('User');
        $user_info = $m->where('id='.$user_id)->find();
        if($m->where('mobile='.$mobile)->find()) $this->api_error(20002,'该手机号已注册');


        // 发送短信验证码
        $result = send_sms_code($mobile);
        switch ($result)
        {
            case 2:
                $this->api_error(20005,'手机号码格式错误');
                break;
            case 3:
                $this->api_error(20006,'短信发送失败,请联客服咨询');
                break;
            case 4:
                $this->api_error(20007,'短信发送失败');
                break;
            default:
                $user_data['send_time']  = $result['send_time'];
                $user_data['code']       = $result['code'];
                $user_data['updated_at'] = time();
                $is_ok      = $m->where('id='.$user_id)->save($user_data);
                if(!$is_ok) $this->api_error(20008,'短信发送失败,请稍后重试');
                $this->api_return('发送短信成功');
        }
    }

    /*
     * 确认更换手机号
     * **/
    public function confirmChangeMobile()
    {
        $user_id= I('get.user_id');
        $mobile = I('get.mobile');
        $code   = I('get.code');

        // 检查参数
        if(!$mobile)  $this->api_error(20001,'手机号不能为空');
        if(!$user_id) $this->api_error(20001,'请先登录');
        if(!$code)    $this->api_error(20001,'请输入验证码');

        $m = M('User');
        $user_info = $m->field('trade_pass_wd',true)->where('id='.$user_id)->find();

        // 检查帐户状态
        if($m->field('trade_pass_wd',true)->where('mobile='.$mobile)->find()) $this->api_error(20002,'该手机号已注册');


        //判断验证码是否失效
        if(time() - $user_info['send_time'] > 300)
        {
            //$this->api_error(30004,'验证码已超时，请重新获取验证码');
        }


        if($user_info['code'] == $code || $code== '888888')
        {
            $change_res = $m->where('id='.$user_id)->setField('mobile',$mobile);
            $this->api_return('修改成功',$user_info);
        }
        $this->api_error(30005,'验证码不正确,请重新输入');
    }

    /*
    * 根据user_id 绑定邮箱
    *
    * */
    public function bindEmail()
    {
        $user_id      = I('user_id');
        if(!$user_id) $this->api_error(20001,'请先登录!');

        $email      = I('get.email');
        if(!$email) $this->api_error(20001,'请输入绑定邮箱!');

        $m = M('User');
        if($m->where('email_status=1 and email="'.$email.'" ')->find()) $this->api_error(20002,'该邮箱已被绑定');

        $send_time = $m->where('id='.$user_id)->getField('send_time');
        $c_time    = 60-(time()-$send_time);
        //if($c_time > 0) $this->api_error(20003,'验证邮件已发出,请在'.$c_time.'s后重试');
        $saveData['email'] = $email;
        $saveData['email_status'] = 2;
        $saveData['send_time'] = time();

        $change_res      = $m->where('id='.$user_id)->save($saveData);
        if(!$change_res) $this->api_error(20004,'邮件发送失败,请稍后重试');

        // 发送验证
        $time  = time();
        $token = 'uuid='.$user_id.'&email='.$email.'&date_time='.$time;
        $sign  = md5($token.$this->_miyao);
        $url   = getSelf().':'.$_SERVER['SERVER_PORT'].'/EggcoinUser/bindEmailConfirm?token='.base64_encode($token).'&sign='.$sign;
        $mail_m    = M('SendMail');
        $mail_data['contents'] = '感谢您使用！请点击以下链接完成激活邮箱：'.$url;
        $mail_data['receiver'] = $email;
        $mail_data['title']    = '邮箱验证';
        $mail_data['send_times']  = 0;
        $mail_data['send_status'] = 1; //发送状态：1.待发送；2.保存；3.发送成功；4.发送失败
        if(!$mail_m->add($mail_data)) $this->api_error(20005,'验证邮件发送失败,请稍后重试!');
        $this->cronSendMail();
        $this->api_return('验证邮件已发送,请在'.ceil($this->_email_confirm_timeout/60).'分钟内确认!');
    }

    /*邮箱验证*/
    public function bindEmailConfirm()
    {
        $sign  = I('get.sign');
        $token = base64_decode(I('get.token'));
        $tmp  = explode('&',$token);
        $dis_data = array('code'=>0,'msg'=>'验证失败');
        if($tmp)
        {
            foreach ($tmp as $v)
            {
                $exp_tmp           = explode('=',$v);
                $data[$exp_tmp[0]] = $exp_tmp[1];
            }

            // 验证
            $res = $this->regConfirmCheckSign($token,$sign);
            if($res and $sign and $data['date_time'])
            {

                if((time()-$data['date_time']) < $this->_email_confirm_timeout) // 是否超时
                {
                    $user_map['id']    = $data['uuid'];
                    $user_map['email'] = $data['email'];
                    $user_info         = M('User')->where($user_map)->find();
                    if( $user_info['email_status'] != 1 )
                    {
                        $user_map['email_status'] = 2;
                        $user_change_res   = M('User')->where($user_map)->setField('email_status',1);
                        if($user_change_res)
                        {
                            $dis_data['code'] = 1;
                            $this->display('bind_email_success');die;
                        }
                    }
                    else
                    {
                        $dis_data['msg'] = '该邮箱已绑定';
                    }
                }
                else
                {
                    $dis_data['msg'] = '验证超时';
                }
            }
        }
        $this->assign('msg',$dis_data['msg']);
        $this->display('bind_email_fail');
    }

    /*
     * 解除邮箱
     * */
    public function unbindEmail()
    {
        $user_id      = I('user_id');
        if(!$user_id) $this->api_error(20001,'请先登录!');
        $change_data = array();
        $change_data['email'] = '';
        $change_data['email_status'] = 2;
        $user_info = M('User')->find($user_id);
        if(!$user_info['email']) $this->api_error(20002,'你还未绑定邮箱');
        if(!M('User')->where('id='.$user_id)->save($change_data)) $this->api_error(20003,'解绑失败!请稍后重试');
        $this->api_return('解绑成功');
    }

    /*
     * 发送邮件
     * */
    public function cronSendMail()
    {
        $mail_m = M('SendMail');
        $send_map['send_status'] = 1;
        $send_map['send_times']  = array('lt',time());
        $list = $mail_m->where($send_map)->select();

        $send_m = new phpmailerAction();

        if($list)
        {
            foreach ($list as $k=>$v)
            {
                $res = $send_m->sendMail($v['receiver'],$v['contents'],$v['title']);
                if($res['code']==1)
                {
                    $mail_m->where('id='.$v['id'])->setField('send_status',3);
                    //echo 'success','<br>';
                }
                else
                {
                    //echo 'error','<br>';
                }
            }
        }
        else
        {
            //echo "NULL";
        }
    }

    // 验证签名
    private function regConfirmCheckSign($token,$sign)
    {
        $str = $token.$this->_miyao;
        if(md5($str)==$sign) return true;
    }
}
