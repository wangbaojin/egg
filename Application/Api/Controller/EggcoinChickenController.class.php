<?php
namespace Api\Controller;
/**
 * Created by PhpStorm.
 * User: wangbaojin
 * Date: 18/2/6
 * Time: 下午12:15
 */
class EggcoinChickenController extends ApiController
{
    // 鸡状态
    private $_chicken_state = array(
        1 => '待认养',
        2 => '释放',
        3 => '锁定',
        4 => '待绑定',
        5 => '已认养'
    );

    // 订单状态
    private $_chicken_order_state = array(
        1 => '待发货',
        2 => '已发货',
        3 => '已完成',
        4 => '失败'
    );

    // 订单支付状态
    private $_chicken_order_pay_state = array(
        1 => '待支付',
        2 => '支付完成',
        3 => '退款中',
        4 => '已退款',
    );

    // 每次可购买数量
    private $_buy_limit = 5;

    /* 首页
     * */
    public function index()
    {
        $user_id = I('user_id');
        if (!$user_id) $this->api_error(20001, '请先登录');

        // 是否有新闻
        $readed_id = M('NewsReaded')->where('user_id=' . $user_id)->getField('news_id');
        $last_id = M('News')->order('id desc')->limit(1)->getField('id');
        $return_data['news_update'] = $readed_id < $last_id ? 1 : 2;

        // 更新用户登陆时间
        M('User')->where('id='.$user_id)->setField('updated_at',time());

        // 认养鸡数
        $return_data['chicken_count'] = M('Chicken')->where('(state=5 or state=4) and user_id=' . $user_id)->count();
        $this->api_return('success', $return_data);
    }

    /*
     * 待收取收益列表
     * */
    public function getCollectChickenDelivery()
    {
        $user_id = I('user_id');
        $m = M('ChickenTodayfeedDelivery');
        $map['state'] = 2;
        $map['user_id'] = $user_id;
        $page = (int)I('page');
        $data['page_limit'] = 10;
        $data['total_count'] = $m->where($map)->count();
        $data['total_page'] = ceil($data['total_count'] / $data['page_limit']);
        $data['now_page'] = ($page > 0 and $page <= $data['total_page']) ? $page : 1;
        $list = $m->where($map)->field('id,egg_weight,income')->page($page, $data['page_limit'])->select();
        if (!$list) $this->api_error(20003, '暂无可收取收益');
        $data['data'] = $list;
        $this->api_return('success', $data);
    }

    /*收取收益*/
    public function collectEgg()
    {
        $user_id = I('get.user_id');
        if (!$user_id) $this->api_error(20001, '请先登录');
        $m = M('ChickenTodayfeedDelivery');

        $map['user_id'] = $user_id;
        $map['state']   = 2;
        $idAry          = $m->where($map)->getField('id',true);
        if (empty($idAry)) $this->api_error(20002, '暂无可收取收益');

        $trans = M();
        $trans->startTrans();

        // 总收益
        $income = $m->where(array('id'=>array('in',$idAry)))->SUM('income');

        // 修改状态
        if (!$m->where(array('id'=>array('in',$idAry)))->setField('state', 3)) {
            $this->api_error(20003, '收取失败,请稍后重试');
        }

        // 修改钱包
        $wallet_map['user_id'] = $user_id;
        // 累计收益
        $total_amount_res = M('Wallet')->where($wallet_map)->setInc( 'total_amount', $income );
        // 当前收益
        $amount_res = M('Wallet')->where($wallet_map)->setInc( 'amount', $income );

        if (!$total_amount_res or !$amount_res)
        {
            $trans->rollback();
            $this->api_error(20003, '收取失败,请稍后重试');
        }

        $trans->commit();
        $this->api_return('succes');
    }

    /*收取收益*/
    public function collectChickenDelivery()
    {
        $user_id = I('get.user_id');
        $delivery_id = I('get.delivery_id');
        if (!$user_id) $this->api_error(20001, '请先登录');
        if (!$delivery_id) $this->api_error(20001, '参数错误');
        $m = M('ChickenTodayfeedDelivery');

        $map['id'] = $delivery_id;
        $map['user_id'] = $user_id;
        $map['state'] = 2;
        $info = $m->where($map)->find();
        if (!$info) $this->api_error(20002, '暂无可收取收益');

        $trans = M();
        $trans->startTrans();

        // 修改状态
        if (!$m->where($map)->setField('state', 3)) {
            $this->api_error(20003, '收取失败,请稍后重试');
        }

        // 修改钱包
        $wallet_map['user_id'] = $user_id;
        // 累计收益
        $total_amount_res = M('Wallet')->where($wallet_map)->setInc('total_amount', $info['income']);
        // 当前收益
        $amount_res = M('Wallet')->where($wallet_map)->setInc('amount', $info['income']);
        if (!$total_amount_res or !$amount_res) {
            $trans->rollback();
            $this->api_error(20003, '收取失败,请稍后重试');
        }
        $trans->commit();
        $this->api_return('succes');
    }

    /*我的鸡列表*/
    public function getUserChicken()
    {
        $m     = M('Chicken');
        $e_a_m = M('EggcoinAccount');
        if(!I('get.user_id')) $this->api_error('请先登录');

        $map['user_id'] = I('user_id');
        if(I('state')) $map['state'] = array('in',explode(',',I('state')));

        $page = (int)I('page');
        $data['page_limit']  = 20;
        $data['total_count'] = $m->where($map)->count();
        $data['total_page']  = ceil($data['total_count']/$data['page_limit']);
        $data['now_page']    = ($page > 0 and $page <= $data['total_page']) ? $page : 1;
        $list =    $m->where($map)->page($page,$data['page_limit'])->select();
        if(!$list) $this->api_error(20003,'还未认领鸡');

        // 处理列表
        foreach ($list as $k=>$v)
        {
            $list[$k]['state_info'] = $this->_chicken_state[$v['state']];

            $list[$k]['eggcoin_account_address'] = $v['eggcoin_account_id'] ? $e_a_m->where('id='.$v['eggcoin_account_id'])->getField('account_address') : '';

            $chicken_batch = M('ChickenBatch')->where('id='.$v['chicken_batch'])->find();

            if(!$chicken_batch) $this->api_error(20004,'获取信息认养鸡失败');

            $list[$k]['breed'] = $chicken_batch['breed'];

            //日龄, 如果是已认养则为:领养时间+初始日龄,其他均是初始日龄
            $list[$k]['age'] = $v['state']==5 ? $chicken_batch['age_in_days']+(int)ceil((time()-$v['created'])/86400) : $chicken_batch['egg_in_days'];
        }

        $data['data'] = $list;
        $this->api_return('success',$data);
    }

    /*
     * 收益
     * */
    public function getUserChickenProfit()
    {
        $m = M('ChickenTodayfeedDelivery');

        $map['state']   = 3;
        $user_id        = (int)I('user_id');
        $map['user_id'] = $user_id;
        $page           = (int)I('page');
        $data['page_limit']  = 20;
        $data['total_count'] = $m->where($map)->count();
        $data['total_page']  = ceil($data['total_count'] / $data['page_limit']);
        $data['now_page']    = ($page > 0 and $page <= $data['total_page']) ? $page : 1;
        $data['amount']      = M('Wallet')->where('user_id='.$user_id)->getField('amount');
        $date_list       = $m->where($map)->page($page, $data['page_limit'])->group('delivery_date')->order('delivery_date desc')->getField('delivery_date', true);
        if (!$date_list) $this->api_error(20003, '暂无可收取收益');

        // 处理列表
        foreach ($date_list as $dk => $dv)
        {
            $tmp_map = $f_tmp = array();
            $tmp_map['delivery_date'] = $dv;
            $tmp_map['state'] = 3;
            $tmp_map['user_id'] = $user_id;
            $tmp_list = $m->where($tmp_map)->select();
            if (!$tmp_list) continue;
            foreach ($tmp_list as $k => $v)
            {
                $tmp = array();
                $tmp['egg_weight']  = $v['egg_weight'].'g';
                $tmp['total_price'] = $v['income'].'元';
                $tmp['day_price']   = round($v['income'] / $v['egg_weight'] * 1000, 2).'元/kg';
                $f_tmp['info_list'][] = $tmp;
            }
            $f_tmp['date'] = $dv;
            $f_tmp['date_time'] = strtotime($dv);
            $data['data'][] = $f_tmp;
        }
        $this->api_return('success', $data);
    }

    /*
     * 获取用户账单
     * */
    public function getUserBill()
    {
        $m   = M('MergeBill');
        $w_m = M('Withdrawals');
        $c_m = M('ChickenTodayfeedDelivery');

        $user_id  = (int)I('user_id');
        $month    = I('month');
        //$month    = strtotime($month) ? $month : date('Y-m');
        if(!$user_id) die('参数错误');


        $map['user_id']      = $user_id;
        if($month) $map['create_year_month'] = $month;
        $page                = (int)I('page');
        $data['page_limit']  = 20;
        $data['total_count'] = $m->where($map)->count();
        $data['total_page']  = ceil($data['total_count'] / $data['page_limit']);
        $data['now_page']    = ($page > 0 and $page <= $data['total_page']) ? $page : 1;
        $list                = $m->where($map)->page($page, $data['page_limit'])->field('user_id',true)->order('create_time desc')->select();
        if (!$list) $this->api_error(20003, '暂无可收取收益');

        // 处理列表
        $newList  = array();
        foreach ($list as $dk => $dv)
        {
            $first_id =  $m->where('user_id='.$user_id.' and create_year_month="'.$dv['create_year_month'].'"')->order('create_time desc')->getField('id');
            
            $tmp_map = $f_tmp = array();
            if($dv['come_from']==1)
            {
                $c_info    = $c_m->where('user_id='.$user_id.' and id='.$dv['oid'])->find();

                if(!$c_info) continue;

                // 鸡舍信息
                $chicken_info = M('Chicken')->where('id='.$c_info['chicken_id'])->find();

                if($chicken_info) $batch_info = M('ChickenBatch')->where('id='.$chicken_info['chicken_batch'])->find();

                $f_tmp['id'] = $dv['id'];
                $f_tmp['come_from'] = $dv['come_from'];
                $f_tmp['come_from_info'] = $batch_info ? $batch_info['breed'] : '海兰褐';
                $f_tmp['title']     = $c_info['age_in_days'].'日龄';
                $f_tmp['egg_weight'] = $c_info['egg_weight'];
                $f_tmp['f_title']   = '编号:'.$chicken_info['chicken_code'];
                $f_tmp['state']     = chicken_delivery_state($c_info['state']);
                $f_tmp['egg_price'] = round($c_info['income'] / $c_info['egg_weight'] * 1000, 2).'元/kg'; //
                $f_tmp['create_date']     = date('m-d H:i',$dv['create_time']); // 月-日 时:分
                $f_tmp['amount_of_money'] = $c_info['income'];
                $f_tmp['create_year_month'] = $dv['create_year_month'];
            }
            if($dv['come_from']==2)
            {
                $w_info    = $w_m->where('user_id='.$user_id.' and id='.$dv['oid'])->find();

                if(!$w_info) continue;

                $f_tmp['id'] = $dv['id'];
                $f_tmp['come_from'] = $dv['come_from'];
                $f_tmp['come_from_info'] = '支付宝';
                $f_tmp['title']     = '提现-'.$w_info['zhifubao_account'];
                $f_tmp['egg_weight'] = '';
                $f_tmp['f_title']   = '';
                $f_tmp['state']     = $w_info['state'] != 2 ? withdrawls_state($w_info['state']) : withdrawls_pay_state($w_info['pay_state']);
                $f_tmp['egg_price'] = '';
                $f_tmp['create_date']     = date('m-d H:i',$dv['create_time']); // 月-日 时:分
                $f_tmp['amount_of_money'] = $w_info['apply_amount'];
                $f_tmp['create_year_month'] = $dv['create_year_month'];
            }
            if($first_id==$dv['id']) $f_tmp['is_first'] = 1;
            $newList[] = $f_tmp;
        }
        $data['data'] = $newList;
        $this->api_return('success', $data);
    }

    /*
     * 收益月份
     * */
    public function getUserBillMonth()
    {
        $m   = M('MergeBill');

        $user_id  = (int)I('user_id');

        if(!$user_id) die('参数错误');


        $map['user_id']      = $user_id;
        $page                = (int)I('page');
        $data['page_limit']  = 20;
        $data['total_count'] = $m->where($map)->group('create_year_month')->count();
        $data['total_page']  = ceil($data['total_count'] / $data['page_limit']);
        $data['now_page']    = ($page > 0 and $page <= $data['total_page']) ? $page : 1;
        $list                = $m->where($map)->page($page, $data['page_limit'])->group('create_year_month')->field('count(*) as total_count,create_year_month')->order('create_time desc')->select();
        if (!$list) $this->api_error(20003, '暂无可收取收益');

        $page_limit = 20;
        foreach ($list as $k=>$v)
        {
            $list[$k]['page_limit'] = $page_limit;
            $list[$k]['total_page'] = ceil($v['total_count']/$page_limit);
        }

        // 处理列表
        $data['data'] = $list;
        $this->api_return('success', $data);
    }

    /*
     *  获取认养鸡类型
     * */
    public function getChickenType()
    {
        $batch_info = getCurrentBatch();
        if (!$batch_info) $this->api_error(20002, 暂无可认养的鸡);
        $list = M('ChickenType')->where('state=1 and chicken_batch='.$batch_info['id'])->select();
        if (!$list) $this->api_error(20002, '暂无可认养的鸡');

        foreach ($list as $k => $v)
        {
            $list[$k]['discount_price'] = $v['discount'] ? round(($v['price'] - $v['discount']),2) : $v['price'];
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
        $data['data']['buy_limit'] = $this->_buy_limit;
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
        $check_res = check_not_null_param($not_null_param, $data);
        if ($check_res) $this->api_error(20001, $check_res);

        // 查询用户
        if(!M('User')->where('id='.$data['user_id'])->find()) $this->api_error(20005,'获取用户信息失败,请重现登录');

        // 检查数量
        if($data['num'] < 0 or $data['num'] > $this->_buy_limit) $this->api_error(20001,'请输入正确的认购数量!');

        // 检查有没有未完成的订单,未完成之前让其先支付前一单(待定)

        $m = M('ChickenOrder');
        if ($res = $m->where('order_sn=' . $data['order_sn'])->find()) {
            //$this->api_error(20002, '订单号错误');
            if($res['num'] != $data['num']) $this->api_error(20005,'认购数量已修改,请重现选择下单');
            $this->api_return('success');
        }

        $data['created'] = $data['updated'] = time();

        $trans = M();
        $trans->startTrans();

        // 类型判断
        $type_info = M('chicken_type')->where('state=1 && id=' . $data['chicken_type'])->find();

        if (!$type_info) $this->api_error(20004, '该类型不存在');

        // 核对批次类型
        if($type_info['chicken_batch'] != $data['chicken_batch']) $this->api_error(20004, '该批次对应类型不存在');

        $price = round($type_info['price'] - $type_info['discount'],2);
        if (!$price) $this->api_error(20004, '该类型母鸡已暂停认养');

        // 是否认购成功、主要判断改该批次是否还有剩余的鸡:
        // $bind_res['code']为1:则成功,0.则失败,失败原因为:$bind_res['msg']
        //$bind_res = $this->lockChicken($data['user_id'], $data['chicken_batch'], $data['chicken_type'], $data['num']);
        $bind_res = $this->lockChicken($data['user_id'], $data['chicken_batch'], $type_info['chicken_type'], $data['num']);

        // 鸡认购锁定成功则继续下单,否则告知已发行完
        if ($bind_res['code'] == 0)
        {
            $trans->rollback();
            $this->api_error(20005, $bind_res['msg']);
        }

        // 订单锁定时间为15分钟,鸡锁定时间为18分钟,多预留了3分钟的时间,请至少三分钟内完成订单记录
        // 支付回调后先依据待定锁定时间,再判断鸡锁定时间,不超时绑定成功后则此次交易成功
        // 下单,允许其有15分钟的支付时间,否则支付超时,认购的鸡也将被释放,鸡库存刷新(该逻辑在支付回调时处理)
        $order_info = array();
        $order_info['lock_time'] = time() + 900;
        $order_info['order_sn']  = $data['order_sn'];
        $order_info['user_id']   = $data['user_id'];
        $order_info['num']       = $data['num'];
        $order_info['chicken_batch'] = $data['chicken_batch'];
        $order_info['chicken_type']  = $data['chicken_type'];
        $order_info['created']       = $order_info['updated'] = time();
        $order_info['total_price']   = $order_info['pay_price'] = $price * $data['num'];
        $order_info['pay_state'] = 1;
        $order_info['state']     = 1;

        $count = M('Chicken')->where('state=4 or state=5')->count();
        if($data['invite_code'] and $count<1)
        {// 如果之前未购买,现在被邀请购买则记录

            $invite_m = M('InviteBuy');

            $user_id = M('User')->where('invite_code='.$data['invite_code'])->getField('id');

            if($user_id)
            {
                $invite_map['invite_user_id'] = $user_id;
                $invite_map['user_id'] = $order_info['user_id'];
                $invite_info = $invite_m->where($invite_map)->find();
                if(!$invite_info)
                {
                    $invite_add['invite_user_id'] = $user_id;
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
                    $invite_m->where($invite_map)->save($invite_update);
                }
            }
            else
            {
                // 记录邀请码无效
                //Log::record('邀请码无效,INFO:' . json_encode($data), 'confirmBuyChicken', true);
            }
        }

        if (!$m->add($order_info))
        {
            $trans->rollback();
            $this->api_error(20006, '认养失败');
        }

        // 下单成功后,返回信息给客户端拉起支付
        $trans->commit();
        $this->api_return('success');
    }

    /*购买回调,此处错误应该做日志记录,调试先返回信息便于调试*/
    public function buyChickenNotifyUrl()
    {
        $order_sn = $_GET['order_sn'];

        $m     = M('ChickenOrder');
        $c_m   = M('Chicken');
        $order = $m->where('order_sn=' . $order_sn)->find();

        if (!$order) $this->api_error(20002, '订单不存在');

        // 订单超时
        if (time() > $order['lock_time'])
        {
            $saveData = array();
            $saveData['updated_at'] = $saveData['pay_time'] = time();
            $saveData['state'] = 4;
            $saveData['pay_state'] = 2;
            $saveData['err_code'] = 'ORDER_TIMEOUT';
            $change_res = $m->where('id=' . $order['id'])->save($saveData);
            //if (!$change_res) Log::record('订单支付超时状态修改失败,INFO:' . json_encode($saveData), 'BUY_ChICKEN', true);
            $this->api_error(20004, '订单超时');
        }

        $saveData = array();
        $saveData['updated_at'] = $saveData['pay_time'] = time();
        $saveData['state']      = 3;
        $saveData['pay_state']  = 2;
        $change_res = $m->where('id=' . $order['id'])->save($saveData);
        if (!$change_res)
        {
            //Log::record('订单状态修改失败,INFO:' . json_encode($saveData), 'BUY_ChICKEN', true);
            $this->api_error(20005, '订单状态修改失败');
        }

        // 类型判断
        $type_info = M('chicken_type')->where('state=1 && id=' . $order['chicken_type'])->find();

        if (!$type_info) $this->api_error(20004, '该类型不存在');

        // 改鸡状态
        $unlock_map = array();
        $unlock_map['user_id']      = $order['user_id'];
        $unlock_map['lock_time']    = array('GT', time());
        $unlock_map['state']        = 3;
        $unlock_map['chicken_type'] = $type_info['chicken_type'];

        $lock_data['state']       = 4; // 状态：1.待认养，2.释放，3.锁定，4.待绑定;5.已认养
        $lock_data['create_date'] = date('Y-m-d',time());
        $lock_data['created']     = $lock_data['updated']   = time();

        $clock_res = $c_m->where($unlock_map)->limit($order['num'])->save($lock_data);

        if ($order['num'] != $clock_res)
        {
            //Log::record('认购鸡失败,INFO:' . json_encode($saveData), 'BUY_ChICKEN', true);
            $saveData['state']    = 1;
            $saveData['err_code'] = 'CHICKEN_EMPTY';

            $m->where('id=' . $order['id'])->save($saveData);
            $this->api_error(20006, '认购鸡失败');
        }

        // 查看有没有邀请购买
        $invite_m = M('InviteBuy');
        $invite_map['order_sn'] = $order_sn;
        $invite_map['buy_state'] = 2;
        $invite_map['user_id']  = $order['user_id'];
        $invite_info = $invite_m->where($invite_map)->find();
        if($invite_info)
        {
            $change_invite_st= $invite_m->where($invite_map)->setField('state',1);
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
        $this->api_return('success');
    }

    /*锁定鸡*/
    private function lockChicken($user_id, $chicken_batch, $chicken_type, $num)
    {
        $data = array('code' => 0, 'msg' => '');
        if (!$user_id or !$chicken_batch or !$chicken_type or !$num)
        {
            $data['msg'] = '缺少参数';
            return $data;
        }

        // 查看该批次是否停止发行(待定)
        $m = M('Chicken');
        $lock_data['user_id']      = $user_id;
        $lock_data['chicken_type'] = $chicken_type;
        $lock_data['lock_time']    = time() + 1080;// 18分钟
        $lock_data['state']        = 3;// 状态：1.待认养，2.释放，3.锁定，4.待绑定;5.已认养

        $lock_map  = array('user_id' => 0, 'state' => 1, 'chicken_batch' => $chicken_batch);
        $clock_res = $m->where($lock_map)->limit($num)->save($lock_data);

        if ($clock_res != $num)
        {
            $data['msg'] = '啊偶!该发行批次的鸡已被认养完,稍后再来碰碰运气吧~~~';
            return $data;
        }
        $data['msg'] = 'success';
        $data['code'] = 1;
        return $data;
    }

    /*解锁超时的鸡*/
    public function unlockChicken()
    {
        $c_m = M('Chicken');
        $unlock_map = array();
        $unlock_map['state'] = 3;
        $unlock_map['lock_time'] = array('LT',time());
        $unlock_data = array('user_id'=>0,'state'=>1);
        $c_m->where($unlock_map)->save($unlock_data);
    }
}