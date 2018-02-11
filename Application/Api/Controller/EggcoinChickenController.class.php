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
        1=>'待认养',
        2=>'释放',
        3=>'锁定',
        4=>'待绑定',
        5=>'已认养'
    );

    // 订单状态
    private $_chicken_order_state = array(
        1=>'待发货',
        2=>'已发货',
        3=>'已完成',
        4=>'失败'
    );

    // 订单支付状态
    private $_chicken_order_pay_state = array(
        1=>'待支付',
        2=>'支付完成',
        3=>'退款中',
        4=>'已退款',
    );

    /* 首页
     * */
    public function index()
    {
        $user_id = I('user_id');
        if(!$user_id) $this->api_error(20001,'请先登录');

        // 是否有新闻
        $readed_id = M('NewsReaded')->where('user_id='.$user_id)->getField('news_id');
        $last_id   = M('News')->order('id desc')->limit(1)->getField('id');
        $return_data['news_update'] = $readed_id < $last_id ? 1 : 2;

        // 认养鸡数
        $return_data['chicken_count'] = M('Chicken')->where('(state=5 or state=4) and user_id='.$user_id)->count();
        $this->api_return('success',$return_data);
    }

    /*
     * 收益
     * */
    public function getUserChickenProfit()
    {
        $user_id = I('user_id');
        /*
         * $page = (int)I('page');
       $data['page_limit']  = 20;
       $data['total_count'] = $m->where($map)->count();
       $data['total_page']  = ceil($data['total_count']/$data['page_limit']);
       $data['now_page']    = ($page > 0 and $page <= $data['total_page']) ? $page : 1;
       $list = $m->where($map)->page($page,$data['page_limit'])->select();
         * */
        $list = array(

            array(
                'created_at'=>'1518162740',
                'info'=>array(
                    array(
                    'egg_weight' => '50g',
                    'day_price' => '2.2元/kg',
                    'total_price' => '0.11元'
                    ),
                    array(
                        'egg_weight' => '50g',
                        'day_price' => '2.2元/kg',
                        'total_price' => '0.11元'
                    )
                )
            ),
            array(
                'created_at'=>'1518076340',
                'info'=>array(
                    array(
                        'egg_weight' => '50g',
                        'day_price' => '2.2元/kg',
                        'total_price' => '0.11元',
                    ),
                    array(
                        'egg_weight' => '50g',
                        'day_price' => '2.2元/kg',
                        'total_price' => '0.11元',
                    )
                ),
            ),
            array(
                'created_at'=>'1517989940',
                'info'=>array(
                    array(
                        'egg_weight' => '50g',
                        'day_price' => '2.2元/kg',
                        'total_price' => '0.11元',
                    ),
                    array(
                        'egg_weight' => '50g',
                        'day_price' => '2.2元/kg',
                        'total_price' => '0.11元',
                    )
                )
            )
        );
        // 钱包
        $return_data['amount'] = M('Wallet')->where('user_id='.$user_id)->getField('amount');

        $return_data['data'] = $list;
        $return_data['page_limit'] = 20;
        $return_data['total_count'] = 20;
        $return_data['total_page'] = 1;
        $return_data['now_page'] = 1;
        $this->api_return('success',$return_data);
    }

    /*
     *  获取认养鸡类型
     * */
    public function getChickenType()
    {
        $list = M('ChickenType')->where('state=1')->select();
        if(!$list) $this->api_error(20002,'暂无可认养的鸡');

        foreach ($list as $k=>$v)
        {
            $list[$k]['discount_price'] = $v['discount'] ? $v['price']-$v['discount'] : $v['price'];
        }
        $data['data'] = $list;
        $this->api_return('success',$data);
    }

    /*
     *  获取当前发行批次鸡
     * */
    private function getCurrentBatch()
    {
        $m = M('ChickenBatch');
        $map = array();
        $map['state']      = 1;
        $map['is_default'] = 1;

        // 发行时间
        //$map['start_time'] = array('lt',time());
        //$map['end_time']   = array('gt',time());
        return $m->where($map)->find();
    }

    /*
     * 认养鸡
     */
   public function buyChicken()
   {
       $batch_info = $this->getCurrentBatch();
       if(!$batch_info) $this->api_error(20002,暂无可认养的鸡);

       $data['data'] = $batch_info;
       $data['data']['order_sn'] = order_no();

       // 可认购量
       $data['data']['buy_limit'] = 10;
       $this->api_return('success',$data);
   }

   /*
    * 确认认养
    * */
   public function confirmBuyChicken()
   {
       $not_null_param = array(
           'user_id'       => '请先登录',
           'order_sn'      => '哎呦!订单号错误',
           'chicken_batch' => '请选择批次',
           'chicken_type'  => '选择个要认养的类型吧',
           'num'           => '您要认购多少只鸡呢?是要买完吗?这么任性的吗?',
       );
       $data = I('post.');
       $check_res = check_not_null_param($not_null_param,$data);
       if($check_res) $this->api_error(20001,$check_res);

       // 检查有没有未完成的订单,未完成之前让其先支付前一单(待定)

       $m = M('ChickenOrder');
       if($res = $m->where('order_sn='.$data['order_sn'])->find())
       {
           $this->api_error(20002,'订单号错误');
       }

       $data['created'] = $data['updated'] = time();

       $trans = M();
       $trans->startTrans();

       // 类型判断
       $type_info = M('chicken_type')->where('state=1 && id='.$data['chicken_type'])->find();
       $price = $type_info['price'] - $type_info['discount'];
       if(!$price) $this->api_error(20004,'该类型母鸡已暂停认养');

       // 是否认购成功、主要判断改该批次是否还有剩余的鸡:
       // $bind_res['code']为1:则成功,0.则失败,失败原因为:$bind_res['msg']
       $bind_res = $this->lockChicken($data['user_id'],$data['chicken_batch'],$data['chicken_type'],$data['num']);

       // 鸡认购锁定成功则继续下单,否则告知已发行完
       if($bind_res['code']==0)
       {
           $trans->rollback();
           $this->api_error(20005,$bind_res['msg']);
       }

       // 订单锁定时间为15分钟,鸡锁定时间为18分钟,多预留了3分钟的时间,请至少三分钟内完成订单记录
       // 支付回调后先依据待定锁定时间,再判断鸡锁定时间,不超时绑定成功后则此次交易成功
       // 下单,允许其有15分钟的支付时间,否则支付超时,认购的鸡也将被释放,鸡库存刷新(该逻辑在支付回调时处理)
       $order_info = array();
       $order_info['lock_time'] = time()+900;
       $order_info['order_sn'] = $data['order_sn'];
       $order_info['user_id'] = $data['user_id'];
       $order_info['num'] = $data['num'];
       $order_info['chicken_batch'] = $data['chicken_batch'];
       $order_info['chicken_type'] = $data['chicken_type'];
       $order_info['created'] = $order_info['updated'] = time();
       $order_info['total_price'] = $order_info['pay_price'] = $price*$data['num'];
       $order_info['pay_state'] = 1;
       $order_info['state'] = 1;
       if(!$m->add($order_info))
       {
           $trans->rollback();
           $this->api_error(20006,'认养失败');
       }

       // 下单成功后,返回信息给客户端拉起支付
       $trans->commit();
       $this->api_return('success');
   }

    /*购买回调,此处错误应该做日志记录,调试先返回信息便于调试*/
    public function buyChickenNotifyUrl()
    {
        
        $order_sn = I('get.order_sn');
        //$order_st = $data['order_st'];
        if(!$order_sn) $this->api_error(20001,'订单号错误');

        // 验证签名(支付回调签名)


        // 订单、订单详情
        $m     = M('ChickenOrder');
        $c_m   = M('Chicken');
        $order = $m->where('order_sn='.$order_sn)->find();
        if(!$order) $this->api_error(20002,'订单不存在');

        // 鸡状态
        if($order['state']!=1) $this->api_error(20003,'订单已处理');

        // 如果支付成功
       // if($order_st='SUCCESS')
        //{
            // 订单超时
            if(time() > $order['lock_time'])
            {
                $saveData = array();
                $saveData['updated_at'] = time();
                $saveData['state'] = 4;
                $saveData['pay_state'] = 2;
                $saveData['err_msg'] = 'ORDER_TIMEOUT';
                $change_res = $m->where('id='.$order['id'])->save($saveData);
                if(!$change_res) Log::record('订单支付超时状态修改失败,INFO:'.json_encode($data),'BUY_ChICKEN',true);
                $this->api_error(20004,'订单超时');
            }

            $saveData = array();
            $saveData['updated_at']= time();
            $saveData['state']     = 3;
            $saveData['pay_state'] = 2;
            $change_res = $m->where('id='.$order['id'])->save($saveData);
            if(!$change_res)
            {
                Log::record('订单状态修改失败,INFO:'.json_encode($data),'BUY_ChICKEN',true);
                $this->api_error(20005,'订单状态修改失败');
            }

            // 改鸡状态
            $unlock_map = array();
            $unlock_map['user_id']       = $order['user_id'];
            $unlock_map['lock_time']     = array('GT',time());
            $unlock_map['state']         = 3;

            $trans = M();
            $trans->startTrans();
            $lock_data['state']          = 4;// 状态：1.待认养，2.释放，3.锁定，4.待绑定;5.已认养
            $clock_res = $c_m->where($unlock_map)->limit($order['num'])->save($lock_data);
            if($order['num'] != $clock_res)
            {
                $trans->rollback();
                Log::record('认购鸡失败,INFO:'.json_encode($data),'BUY_ChICKEN',true);
                $this->api_error(20006,'认购鸡失败');
            }
            $trans->commit();
            $this->api_return('success');
        //}

        //其他状态
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
        //$list = $c_m->where($unlock_map)->select();
    }

   /*锁定鸡*/
   private function lockChicken($user_id,$chicken_batch,$chicken_type,$num)
   {
       $data = array('code'=>0,'msg'=>'');
       if(!$user_id or !$chicken_batch or !$chicken_type or !$num)
       {
           $data['msg'] = '缺少参数';return $data;
       }

       // 查看该批次是否停止发行(待定)
       $m = M('Chicken');
       $lock_data['user_id']       = $user_id;
       $lock_data['chicken_type']  = $chicken_type;
       $lock_data['lock_time']     = time()+1080;// 18分钟
       $lock_data['state']         = 3;// 状态：1.待认养，2.释放，3.锁定，4.待绑定;5.已认养

       $lock_map = array('user_id'=>0,'state'=>1,'chicken_batch'=>$chicken_batch);
       $clock_res = $m->where($lock_map)->limit($num)->save($lock_data);

       if( $clock_res != $num )
       {
           $data['msg'] = '认购数量已不足';
           return $data;
       }
       $data['msg']  = 'success';
       $data['code'] = 1;
       return $data;
   }

   /*
    * 获取我认养的鸡
    * */
   public function getUserChicken()
   {
       $m = M('Chicken');
       $e_a_m = M('EggcoinAccount');
       if(!I('get.user_id')) $this->api_error('请先登录');

       $map['user_id'] = I('user_id');
       if(I('state'))
       {
           $stateAry     = explode(',',I('state'));
           $map['state'] = array('in',$stateAry);
       }

       $page = (int)I('page');
       $data['page_limit']  = 20;
       $data['total_count'] = $m->where($map)->count();
       $data['total_page']  = ceil($data['total_count']/$data['page_limit']);
       $data['now_page']    = ($page > 0 and $page <= $data['total_page']) ? $page : 1;
       $list = $m->where($map)->page($page,$data['page_limit'])->select();
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
           $list[$k]['age'] = $v['state']==5 ? $chicken_batch['egg_in_days']+(int)ceil((time()-$v['created'])/86400) : $chicken_batch['egg_in_days'];
       }

       $data['data'] = $list;
       $this->api_return('success',$data);
   }
}
