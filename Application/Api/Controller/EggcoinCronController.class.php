<?php
namespace Api\Controller;
/**
 * Created by PhpStorm.
 * User: wangbaojin
 * Date: 18/2/6
 * Time: 下午12:15
 */
class EggcoinCronController extends ApiController
{

    /*各个鸡结算,每天12点以后到5点结算前一天的收益*/
    public function chicken_todayfeed_delivery()
    {
        // 结算日期
        $delivery_date = I('get.date') ? I('get.date') : date('Y-m-d',time()-86400);
        //$delivery_date = '2018-02-21';
        echo $delivery_date.'结算<hr>';

        // 已经结算的
        $AlreadySettledChickenIdAry = $this->getAlreadySettledChickenIdAry($delivery_date);
        echo "<pre>";
        // 待结算的
        $chicken_list = $this->getChicken($delivery_date,$AlreadySettledChickenIdAry);

        foreach ($chicken_list as $k=>$v)
        {
            // 批次结算详情
            $delivery_info = $this->getDelivery($v['chicken_batch'],$v['age_in_days']);

            if(!$delivery_info) die('获取该批次'.$v['age_in_days'].'日龄的结算信息失败,可能是还未结算');

            // 结算单只鸡
            $delivery_res = $this->deliveryChickenIncome($v,$delivery_info,$delivery_date);
            print_r($delivery_res);
        }
    }

    /*获取需要结算的鸡,规则为认养且绑定成功,时间至少提前一天*/
    private function getChicken($delivery_date,$ChickenIdAry=array())
    {
        $map['state'] = 5;
        if($ChickenIdAry) $map['id'] = array('not in',$ChickenIdAry);
        $map['create_date'] = array('lt',$delivery_date);
        $delivery_list = M('Chicken')->field('chicken_code,created,chicken_code,updated,lock_time,state',true)->where($map)->limit(100)->select();

        if($delivery_list)
        {
            foreach ($delivery_list as $k=>$v)
            {
                $batch_info =  M('ChickenBatch')->where('id='.$v['chicken_batch'])->find();
                $delivery_list[$k]['breed'] = $batch_info['breed'];
                $delivery_list[$k]['delivery_date'] = $delivery_date;
                $delivery_list[$k]['age_in_days'] = $batch_info['age_in_days']+(strtotime($delivery_date)-strtotime($v['create_date']))/86400;
            }
        }
        return $delivery_list;

    }

    /*获取已经结算的鸡*/
    private function getAlreadySettledChickenIdAry($delivery_date)
    {
        $map['delivery_date'] = $delivery_date;
        $idAry = M('ChickenTodayfeedDelivery')->where($map)->getField('chicken_id',true);
        return $idAry;
    }

    /*获取批次收益*/
    private function getDelivery($chicken_batch,$age_in_days)
    {
        if(!$chicken_batch or !$age_in_days) return;
        $map['chicken_batch'] = $chicken_batch;
        $map['age_in_days'] = $age_in_days;
        $map['state']  = 2;
        $delivery_list = M('ChickenbatchTodayfeedDelivery')->where($map)->field('state',true)->find();

        if(!$delivery_list) return;

        $death_map['age_in_days']   = array('ELT',$age_in_days);
        $death_map['chicken_batch'] = $chicken_batch;
        // 计算死淘
        $delivery_list['death']  = M('ChickenbatchTodayfeedDelivery')->where($death_map)->field('state',true)->SUM('death');
        // 鸡的总数
        $delivery_list['amount'] = M('ChickenBatch')->where('id='.$chicken_batch)->getField('amount');
        // 饲料转化为g
        $delivery_list['feed_weight'] = $delivery_list['feed_weight']*1000;// 单位:g
        // 蛋重转化为g
        $delivery_list['lay_eggs_weight'] = $delivery_list['lay_eggs_weight']*1000;//单位:g
        return $delivery_list;
    }

    /*获取钱包信息*/
    private function getUserWallet($user_id)
    {
        $wallet =  M('Wallet')->where('user_id='.$user_id)->field('feed_amount,arrears_amount')->find();
        if(!$wallet) return;
        $wallet['feed_amount'] = $wallet['feed_amount']*1000;
        return $wallet;
    }

    /*设置钱包饲料及欠款*/
    private function changeUserWalletFeedAndArrears($user_id,$feed_amount='',$arrears_amount='')
    {
        $wallet = $this->getUserWallet($user_id);
        if(!$wallet) return;

        if($feed_amount)    $saveData['feed_amount']   =  $wallet['feed_amount']/1000 + $feed_amount/1000;
        if($arrears_amount) $saveData['arrears_amount'] = $arrears_amount + $wallet['arrears_amount'];
        if($saveData)
        {
            return M('Wallet')->where('user_id='.$user_id)->save($saveData);
        }
    }

    /*记录每只鸡的收益*/
    private function deliveryChickenIncome($chicken_info,$delivery_info,$delivery_date)
    {
        $return_data = array('code'=>0,'msg'=>'');
        $delivery_not_null_param = array(
            'egg_price' => '鸡蛋总价不可为空',
            'lay_eggs'  => '总产蛋不可为空',
            'lay_eggs_weight'  => '总蛋重不可为空',
            'feed_weight'  => '饲料消耗不可为空',
            'amount'  => '批次总栏数不可为空',
            //'expenses'  => '现金支出不可为空',
        );
        $chicken_not_null_param = array(
            'id'  => '鸡id不能为空',
            'user_id'  => '用户id不能为空',
            'chicken_batch'  => '批次不可为空',
            'chicken_type' => '认养类型不可为空',
            'create_date' => '认养开始时间不可为空',
            'breed' => '母鸡品种不可为空',
            'age_in_days' => '鸡龄不可为空',
            //'eggcoin_account_id' => '钱包地址不可为空',
            //'delivery_date'  => '结算日期不能为空',
        );
        $delivery_check_res = check_not_null_param($delivery_not_null_param,$delivery_info);
        $chicken_check_res = check_not_null_param($chicken_not_null_param,$chicken_info);
        if($delivery_check_res or $chicken_check_res)
        {
            $return_data['msg'] = $delivery_check_res ? $delivery_check_res : $chicken_check_res;
            // 错误日志
            return $return_data;
        }


        // 认养类型
        $type_info = M('chicken_type')->where('state=1 && id='.$chicken_info['chicken_type'])->find();
        if(!$type_info)
        {
            $return_data['msg'] = '该类型不存在';return $return_data;
        }

        /*剩余鸡数 ＝（发行量－今日总死淘）
         *
         * 每只投料=总投料/剩余鸡数
         * 每只支出=现金支出/剩余鸡数
         * 每只投料=总投料/剩余鸡数
         * */

        $now_amount = $delivery_info['amount']-$delivery_info['death'];
        $arr = array();
        $arr['chicken_id'] = $chicken_info['id'];
        $arr['user_id']    = $chicken_info['user_id'];
        $arr['age_in_days']    = $chicken_info['age_in_days'];
        $arr['delivery_date'] = $delivery_date;
        if($chicken_info['eggcoin_account_id']) $arr['eggcoin_account_id'] = $chicken_info['eggcoin_account_id'];
        // 每只鸡单日支出 = 该批次当日总消耗 / 该批次发行数-总死淘(即当前批次剩余存栏)
        $arr['feed_weight'] = round($delivery_info['feed_weight']/$now_amount,5); // 饲料消耗
        $arr['egg_weight'] = round($delivery_info['lay_eggs_weight']/$now_amount,5);// 每颗鸡蛋重
        if($delivery_info['expenses']) $arr['expenses'] = round($delivery_info['expenses']/$now_amount,5); // 现金支出

        // 每只鸡当日收入 = 该批次当日总收入 / 该批次发行数
        $arr['egg_num'] = round($delivery_info['lay_eggs']/$delivery_info['amount'],5);// 用户收取蛋数
        $arr['income'] = round($delivery_info['egg_price']/$delivery_info['amount'],5);// 用户收入
        if($delivery_info['eggcoin_income']) $arr['eggcoin_income'] = round($delivery_info['eggcoin_income']/$now_amount,5); // 数字货币收入
        if($delivery_info['avg_eggcoin_income']) $arr['eggcoin_income'] = $delivery_info['avg_eggcoin_income']; // 平均每只鸡数字货币收入

        if($chicken_info['eggcoin_account_id']) $arr['eggcoin_account_id'] = $chicken_info['eggcoin_account_id'];
        $arr['created_at'] = strtotime($delivery_date)+(time()-strtotime(date('Y-m-d')));
        $arr['state'] = 2;//1.待确认；2.待结算；3.已结算
        /*echo "delivery";
        print_r($delivery_info);
        echo "<hr>";
        echo "chicken_info";
        print_r($chicken_info);
        echo "<hr>";
        print_r($arr);*/

        $trans = M();
        $trans->startTrans();

        $delivery_res = M('ChickenTodayfeedDelivery')->add($arr);
        if(!$delivery_res)
        {
            $return_data['msg'] = '添加失败';
            return $return_data;
        }
        // 饲料流水,chicken_type类型为1的不需要扣款,直接成功,记录流水即可,不等于1得则需要扣款
        if($chicken_info['chicken_type']!=1)
        {
            // 查询钱包
            $wallet_info = $this->getUserWallet($arr['user_id']);
            $cha = $wallet_info['feed_amount'] - $arr['feed_weight'];
            // 备份用于修改钱包金额
            $feed_weight = $arr['feed_weight'];

            // 说明钱包不够扣除
            if($cha < 0)
            {

                // 说明还要余额剩余,但是不够扣除饲料,需要记一条正常扣除剩余余额的流水,还要有一条待补余额的流水
                if($wallet_info['feed_amount'] >0)
                {
                    $cha_amount = $cha;
                    // 此时正常扣除的饲料为剩余的饲料
                    $arr['feed_weight'] = $wallet_info['feed_amount'];
                }
                else
                {// 说明完全不够扣除
                    $cha_amount = $feed_weight;
                    // 所以正常扣除的饲料为0;无需记录
                    $arr['feed_weight'] = 0;
                }

                // 饲料消耗待补流水记录
                $record = array();
                $record['user_id'] = $arr['user_id'];
                $record['amount']  = $cha_amount;
                $record['reason_source_id'] = $delivery_res;
                $record['reason_type'] = 3;//事由类型id：1.买入、2.饲料买入，3.投喂；4.支出；5.收益；6.饲料补扣；7.支出补扣'8.赠送;9.奖励
                $record['reason_narration'] = $chicken_info['age_in_days'].'日龄'.$chicken_info['breed'].'1羽';
                $record['state'] = 3;//状态：1.成功;2.失败;3.待处理
                $record['unit'] = 'g';
                $record_res = addRecord($record);

                if($record_res['code']==0)
                {
                    $trans->rollback();
                    $return_data['msg']  = '饲料消耗记录失败'.$record_res['msg'];
                    return $return_data;
                }
            }

            $wallet_change = $this->changeUserWalletFeedAndArrears($arr['user_id'],-$feed_weight,$arr['expenses']);
            if(!$wallet_change)
            {
                $trans->rollback();
                $return_data['msg']  = '钱包扣款失败';
                return $return_data;
            }
        }

        // 饲料消耗流水记录
        if($arr['feed_weight'])
        {
        $record = array();
        $record['user_id'] = $arr['user_id'];
        $record['amount']  = -$arr['feed_weight'];
        $record['reason_source_id'] = $delivery_res;
        $record['reason_type'] = 3;//事由类型id：1.买入、2.饲料买入，3.投喂；4.支出；5.收益；6.饲料补扣；7.支出补扣'8.赠送;9.奖励
        $record['reason_narration'] = $chicken_info['age_in_days'].'日龄'.$chicken_info['breed'].'1羽';
        $record['state'] = 1;//状态：1.成功;2.失败;3.待处理
        $record['unit'] = 'g';
        $record_res = addRecord($record);
        if($record_res['code']==0)
        {
            $trans->rollback();
            $return_data['msg']  = '饲料消耗记录失败'.$record_res['msg'];
            return $return_data;
        }}

        // 现金消耗
        if($arr['expenses'])
        {
            $record = array();
            $record['user_id'] = $arr['user_id'];
            $record['amount']  = -$arr['expenses'];
            $record['reason_source_id'] = $delivery_res;
            $record['reason_type'] = 4;//事由类型id：1.买入、2.饲料买入，3.投喂；4.支出；5.收益；6.饲料补扣；7.支出补扣'8.赠送;9.奖励
            $record['reason_narration'] = $chicken_info['age_in_days'].'日龄'.$chicken_info['breed'].'1羽';
            $record['state'] = $chicken_info['chicken_type'] == 1 ? 1 : 3; //状态：1.成功;2.失败;3.待处理
            $record['unit'] = '元';
            $record_res = addRecord($record);
            if($record_res['code']==0)
            {
                $trans->rollback();
                $return_data['msg']  = '饲料消耗记录失败!'.$record_res['msg'];
                return $return_data;
            }
        }

        /**
        'user_id' => '用户不能为空',
        'eggcoin_account_id' => '钱包id不能为空',
        'amount' => '请填写数量',
        'reason_type' => '请填写流水类型',
        'reason_narration' => '请填写流水标题',
        'state' => '请填写状态',
         */
        // 数字发行
        if($arr['eggcoin_income'])
        {
            $eggcoin_data = array();
            $eggcoin_data['user_id'] = $arr['user_id'];
            $eggcoin_data['chicken_id'] = $chicken_info['id'];
            $eggcoin_data['eggcoin_account_id'] = $chicken_info['eggcoin_account_id'];
            $eggcoin_data['amount'] = (int)ceil($arr['eggcoin_income']);
            $eggcoin_data['reason_type'] = 1;//事由类型id：1.收益；2.赠送；3.奖励'
            $eggcoin_data['reason_narration'] = '母鸡产出';//事由名称
            $eggcoin_data['reason_source_id'] = $delivery_res;
            $eggcoin_data['state'] = 1;//状态：1.成功;2.失败;3.待处理'

            // 钱包地址
            $eggcoin_account = getEggcoinAccountInfoById($chicken_info['eggcoin_account_id']);
            if(!$chicken_info['eggcoin_account_id'] or !$eggcoin_account or !$eggcoin_account['account_address'])
            {
                $eggcoin_data['state']    = 3;//状态：1.成功;2.失败;3.待处理'
                $eggcoin_data['err_code'] = 'ADDRESS_NULL';
            }
            else
            {
                $issueEggCoin_res = issueEggCoin($eggcoin_data['amount'],$eggcoin_account['account_address']);
                if($issueEggCoin_res['retcode']!=1)
                {
                    $eggcoin_data['state']    = 3;//状态：1.成功;2.失败;3.待处理'
                    $eggcoin_data['err_code'] = 'ISSUE_ERROR';
                }
            }

            $eggcoin_record = addEggcoinRecord($eggcoin_data);
            if($eggcoin_record['code']==0)
            {
                $trans->rollback();
                $return_data['msg']  = '数字发行记录失败!'.$eggcoin_record['msg'];
                return $return_data;
            }
        }

        $return_data['code'] = 1;
        $return_data['msg']  = 'success';
        $trans->commit();
        return $return_data;
    }
}