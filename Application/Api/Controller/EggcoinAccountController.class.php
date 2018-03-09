<?php
namespace Api\Controller;
/**
 * Created by PhpStorm.
 * User: wangbaojin
 * Date: 18/2/6
 * Time: 下午12:15
 */
class EggcoinAccountController extends ApiController
{

    /*
     *  添加常用数字货币钱包
     * */
    public function addCommonEggcoinAccount()
    {
        $not_null_param = array(
            'user_id'            => '请先登录',
            'eggcoin_account_address' => '请填写钱包地址',
            'custom_name' => '给钱包起个可爱的名字吧'
        );
        $check_res = check_not_null_param($not_null_param,I('post.'));
        if($check_res) $this->api_error(20001,$check_res);

        // 钱包地址id
        $eggcoin_account_id = getEggcoinAccountId(I('post.eggcoin_account_address'));
        if(!$eggcoin_account_id) $this->api_error(20005,'钱包记录失败,请稍后重试');

        // 是否已添加
        $map['user_id'] = I('post.user_id');
        $map['eggcoin_account_id'] = $eggcoin_account_id;
        if(M('CommonEggcoinAccount')->where($map)->find()) $this->api_error(20002,'你已经添加过该地址了');

        // 添加
        $data['custom_name'] = I('post.custom_name');
        $data['user_id']     = I('post.user_id');
        $data['eggcoin_account_id'] = $eggcoin_account_id;
        if(!M('CommonEggcoinAccount')->add($data)) $this->api_error(20002,'啊偶!服务器开小差了,请稍后再试');
        $this->api_return('添加成功');
    }

    /*删除常用货币钱包*/
    public function delCommonEggcoinAccount()
    {
        $not_null_param = array(
            'user_id'            => '请先登录',
            'eggcoin_account_id' => '请选择要删除钱包'
        );
        $check_res = check_not_null_param($not_null_param,I('post.'));
        if($check_res) $this->api_error(20001,$check_res);

        $map['user_id'] = I('post.user_id');
        $map['eggcoin_account_id'] = I('post.eggcoin_account_id');
        if(!M('CommonEggcoinAccount')->where($map)->delete()) $this->api_error(20002,'删除失败!请稍后重试');
        $this->api_return('success');
    }

    /*修改常用货币钱包备注*/
    public function changeCustomNameCommonEggcoinAccount()
    {
        $not_null_param = array(
            'user_id'            => '请先登录',
            'eggcoin_account_id' => '请选择要修改的钱包',
            'custom_name' => '请填写自定义名称',
        );
        $check_res = check_not_null_param($not_null_param,I('post.'));
        if($check_res) $this->api_error(20001,$check_res);

        $map['user_id'] = I('post.user_id');
        $map['eggcoin_account_id'] = I('post.eggcoin_account_id');
        if(!M('CommonEggcoinAccount')->where($map)->setField('custom_name',I('post.custom_name'))) $this->api_error(20002,'更改失败!请稍后重试');
        $this->api_return('success');
    }

    /*
     *  获取常用数字货币钱包
     * */
    public function getCommonEggcoinAccount()
    {
        $not_null_param = array(
            'user_id'            => '请先登录'
        );
        $user_id = I('get.user_id');
        $check_res = check_not_null_param($not_null_param,I('get.'));
        if(!$user_id) $this->api_error(20001,$check_res);
        
        $list = M('CommonEggcoinAccount')->where('user_id='.$user_id)->select();
        if(!$list) $this->api_error(20002,'啊偶!你还没有添加常用钱包地址,先去添加吧');
        foreach ($list as $k=>$v)
        {
            $eggcoin_account = getEggcoinAccountInfoById($v['eggcoin_account_id']);
            $list[$k]['eggcoin_account_address'] = $eggcoin_account['account_address'];
        }
        $data['data'] = $list;
        $this->api_return('success',$data);
    }

   /*
    * 获取我的数字货币钱包
    * */
   public function getUserEggcoinAccount()
   {
       $m = M('EggcoinAccount');
       if(!I('user_id')) $this->api_error(20002,'请先登录');

       // 获取已绑定的鸡
       $c_map['user_id'] = I('user_id');
       $c_map['state']   = 5;
       $eggcoin_account_idAry = M('Chicken')->where($c_map)->field('eggcoin_account_id')->group('eggcoin_account_id')->getField('eggcoin_account_id',true);

       if(!$eggcoin_account_idAry) $this->api_error(20003,'暂无绑定的钱包');

       // 列表
       $map['id'] = array('in',$eggcoin_account_idAry);
       $list = $m->where($map)->select();

       // 处理列表
       foreach ($list as $k=>$v)
       {
            $list[$k]['chicken_num'] = M('Chicken')->where('state=5 and eggcoin_account_id='.$v['id'].' and user_id='.I('user_id'))->count();
       }

       $data['data'] = $list;
       $this->api_return('success',$data);
   }

   /*
    * 鸡绑定数字货币钱包
    * */
   public function chickenBindEggcoinAccount()
   {
       $not_null_param = array(
           'user_id'            => '请先登录',
           'chicken_id'         => '请选择要绑定的鸡',
           'eggcoin_account_address' => '请选择要绑定的钱包'
       );
       $data = I('post.');
       $check_res = check_not_null_param($not_null_param,$data);
       if($check_res) $this->api_error(20001,$check_res);

       // 鸡id,','拼接拆分
       $chicken_id_ary = explode(',',$data['chicken_id']);

       // 钱包地址id
       $eggcoin_account = getEggcoinAccountId($data['eggcoin_account_address']);
       if(!$eggcoin_account or !$eggcoin_account['id']) $this->api_error(20005,'钱包记录失败,请稍后重试');

       $m = M('Chicken');
       foreach ($chicken_id_ary as $k=>$v)
       {
           if(!(int)$v) $this->api_error(20002,'请传入正确的绑定鸡ID');

           $find_map = array();
           $find_map['user_id'] = $data['user_id'];
           $find_map['id'] = $v;
           $find_map['state'] = 4;//'状态：1.待认养，2.释放，3.锁定，4.待绑定;5.已认养
           if(!$m->where($find_map)->find()) $this->api_error(20004,'请传入正确的绑定鸡ID');
       }

       // 检查鸡
       $bind_data = array();
       $bind_data['eggcoin_account_id'] = $eggcoin_account['id'];
       $bind_data['created'] = $bind_data['updated'] = time();
       $bind_data['create_date'] = date('Y-m-d',time());
       $bind_data['state']   = 5;
       $find_map['id'] = array('in',$chicken_id_ary);
       if(!$m->where($find_map)->save($bind_data)) $this->api_error(20006,'绑定失败,请稍后重试');
       $this->api_return('绑定成功');
   }
}