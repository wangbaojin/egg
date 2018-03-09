<?php
namespace Api\Controller;
/**
 * Created by PhpStorm.
 * User: wangbaojin
 * Date: 18/2/6
 * Time: 下午12:15
 */
class EggcoinAdvertisementController extends ApiController
{

    /* 首页
     * */
    public function get()
    {
        // 是否有广告
        $return_data['id']  = 1;
        $return_data['pic'] = 'https://ss0.bdstatic.com/94oJfD_bAAcT8t7mm9GUKT-xh_/timg?image&quality=100&size=b4000_4000&sec=1519994080&di=d15deab713a2f4baee29d54973c3bb85&src=http://img3.duitang.com/uploads/item/201606/17/20160617070813_5yiuJ.jpeg';
        $return_data['article_url'] = 'http://zhihu.com';
        $return_data['title'] = '十块钱三件,件件十块钱';
        //$this->api_return('success', $return_data);
    }
}