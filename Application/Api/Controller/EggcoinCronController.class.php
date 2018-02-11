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
    /*收益结算*/
    public function todayDelivery()
    {
        $delivery_list = M('ChickenbatchTodayfeedDelivery')->where('state=2')->select();
        echo "<pre>";
        print_r($delivery_list);
    }
}