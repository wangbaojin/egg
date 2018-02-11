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
        //$delivery_date = date('Y-m-d',time()-86400);
        $delivery_date = date('Y-m-d');

        // 已经结算的
        $AlreadySettledChickenIdAry = $this->getAlreadySettledChickenIdAry($delivery_date);
        echo "<pre>";
        // 待结算的
        $chicken_list = $this->getChicken($delivery_date,$AlreadySettledChickenIdAry);
        //print_r($chicken_list);die;

        foreach ($chicken_list as $k=>$v)
        {
            // 结算详情
            $trans = M();
            $trans->startTrans();
            $delivery_info = $this->getDelivery($v['chicken_batch'],$v['age_in_days']);
            if(!$delivery_info) $this->api_error(20001,'获取批次信息失败');


            $delivery_res = $this->deliveryChickenIncome($v,$delivery_info);
            print_r($delivery_info);echo 1;
            print_r($delivery_res);echo 1;

            //
            $trans->rollback();
            die;
            //$this->deliveryChickenIncome($v,$delivery_info);
        }

        echo "<pre>";
        print_r($chicken_list);
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

        $death_map['age_in_days']   = array('ELT',$age_in_days);
        $death_map['chicken_batch'] = $chicken_batch;
        $delivery_list['death']  = M('ChickenbatchTodayfeedDelivery')->where($death_map)->field('state',true)->SUM('death');
        $delivery_list['amount'] = M('ChickenBatch')->getField('amount');
        $delivery_list['feed_weight'] = $delivery_list['feed_weight']*1000;// 单位:g
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
        if($feed_amount)    $saveData['feed_amount']   =  ($wallet['feed_amount']*1000) + $wallet['feed_amount'];
        if($arrears_amount) $saveData['arrears_amount'] = ($wallet['arrears_amount']) + $wallet['arrears_amount'];
        if($saveData)
        {
            return M('Wallet')->where('user_id='.$user_id)->save($saveData);
        }
    }

    /*记录每只鸡的收益*/
    private function deliveryChickenIncome($chicken_info,$delivery_info)
    {
        $return_data = array('code'=>0,'msg'=>'');
        $delivery_not_null_param = array(
            'delivery_date'  => '结算日期不能为空',
            'chicken_batch'  => '批次不可为空',
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
            'age_in_days' => '鸡龄不可为空'
        );
        $delivery_check_res = check_not_null_param($delivery_not_null_param,$delivery_info);
        $chicken_check_res = check_not_null_param($chicken_not_null_param,$chicken_info);
        if($delivery_check_res or $chicken_check_res)
        {
            $return_data['msg'] = $delivery_check_res ? $delivery_check_res : $chicken_check_res;
            // 错误日志
            return $return_data;
        }


        /*剩余鸡数 ＝（发行量－今日总死淘）
         *
         * 每只投料=剩余鸡数/总投料
         * 每只支出=剩余鸡数/现金支出
         * 每只投料=剩余鸡数/总投料*/
        $now_amount = $delivery_info['amount']-$delivery_info['death'];
        $arr['chicken_id'] = $chicken_info['id'];
        $arr['user_id']    = $chicken_info['user_id'];
        $arr['delivery_date'] = $delivery_info['delivery_date'];
        $arr['feed_weight'] = $now_amount/$delivery_info['feed_weight'];
        $arr['egg_weight'] = $now_amount/$delivery_info['egg_weight'];
        $arr['egg_num'] = $now_amount/$delivery_info['lay_eggs'];
        $arr['income'] = $now_amount/$delivery_info['egg_price'];
        if($delivery_info['eggcoin_income']) $arr['eggcoin_income'] = $now_amount/$delivery_info['eggcoin_income'];
        if($delivery_info['expenses']) $arr['expenses'] = $now_amount/$delivery_info['expenses'];
        if($chicken_info['eggcoin_account_id']) $arr['eggcoin_account_id'] = $chicken_info['eggcoin_account_id'];
        $arr['created_at'] = time();
        $arr['age_in_days'] = $delivery_info['age_in_days'];
        if(!M('ChickenTodayfeedDelivery')->add($arr))
        {
            $return_data['msg'] = '添加失败';
        }
        else
        {
            $return_data['code'] = 1;
            $return_data['msg']  = '添加成功';
            $return_data['data'] = $return_data;
        }
        return $return_data;
    }

}