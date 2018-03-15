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
        $return_data['id']  = 3;
        $return_data['pic'] = 'http://api.lianyangji.io/Public/images/Api/EggcoinAdvertisement.PNG';
        $return_data['article_url'] = 'http://mp.weixin.qq.com/s/1LdMn7R_e5QM8BGdeo-RPQ';
        $return_data['title'] = '重磅!区块链养鸡福利';
        $this->api_return('success', $return_data);
    }
}