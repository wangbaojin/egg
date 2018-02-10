<?php
/**
 * Created by PhpStorm.
 * User: lkk
 * Date: 17/11/13
 * Time: 下午5:19
 */

namespace Api\Controller;

use Think\Controller;

class AdminIndexController extends Controller
{
    public function index()
    {
        // 验证登录
        $uid = session('uid');
        if(!$uid) redirect(U('AdminLogin/login'),2, '还未登录,正在前往登录...');

        $map['offline_user_id']  = $uid;
        $map['status']           = array('neq',4);
        if(I('get.real_name'))   $map['real_name'] = array('real_name','%'.I('get.title').'%');
        if(I('get.tel'))         $map['tel']       = I('get.tel');
        if(I('get.create_date'))
        {
            $b_time = strtotime(I('get.create_date'));
            $map['create_time'] = array('between',array($b_time,$b_time+86400));
        }

        $limit= 5;
        $m    = M('OfflineQudaoOrder');
        $count= $m->where($map)->count();
        $total_page = ceil($count/$limit);
        $_GET['p']  = ($_GET['p'] and $_GET['p' ] <= $total_page) ? $_GET['p'] : 1;
        $list = $m->ORDER('id desc')->WHERE($map)->page($_GET['p'].','.$limit)->SELECT();
        $page = new \Think\Page($count,$limit);
        $show = $page->show();
        $this->assign('_page',$show);

        // 处理数据
        while (list($k,$v)=each($list))
        {
            $list[$k] = $this->disposeData($v);
        }

        $this->assign('list',$list);
        $this->assign('count',$count);
        $this->assign('name',session('name'));
        $this->display();
    }

    public function add()
    {
        $uid = session('uid');
        if(!$uid) redirect(U('OfflineUser/login'),2, '还未登录,正在前往登录...');

        if(I('post.') && $data=I('post.'))
        {
            sleep(1);

            // 检查发货时间
            if(time()-strtotime($data['delivery_time']) > 50400) die('两点之后下单请选择第二天发货');

            // 商品是否下架
            $goods_map['id'] = $data['goods_id'];
            $goodsInfo = M('EggGoods')->where($goods_map)->find();
            if($goodsInfo['status']==2) die('该商品已下架');

            // 检查商品库存

            // 录入订单
            $m = M('OfflineQudaoOrder');
            $data['order_sn']        = $this->getOrderNo();
            $data['offline_user_id'] = $uid;
            $data['create_time']     = time();
            $data['goods_name']      = $goodsInfo['goods_name'];
            $data['goods_price']     = $goodsInfo['price'];

            // 来源
            $come_from = M('OfflineUserDistributionFrom')->where('offline_user_id='.$uid)->getField('come_from_id');
            if(!$come_from) die('渠道未设置来源,请联系管理员!');

            $data['come_from']       = $come_from;
            $info = $m->add($data);
            if($info)
            {
                $msg = 'success';
            }
            else
            {
                $msg = '下单失败';
            }
            die($msg);
        }
        $goodsList    = M('EggGoods')->where('status=1')->order('come_from')->select();
        $provinceList = M('ZjisheLocation')->where('grade=1')->select();
        $this->assign('goodsList',$goodsList);
        $this->assign('provinceList',$provinceList);
        $this->assign('name',session('name'));
        $this->display('add');
    }

    /* 撤销订单*/
    public function revoke()
    {
        $id = I('get.id');
        if(!$id) die('请选择要撤销的订单');

        if(M('OfflineQudaoOrder')->where('id='.$id)->setField('status',4))
        {
            die('success');
        }
        die('撤销失败!请稍后重试');
    }

    /*excel导出数据*/
    public function  exportListExcel() {

        $uid = session('uid');
        if(!$uid) redirect(U('OfflineUser/login'),2, '还未登录,正在前往登录...');

        $map['offline_user_id']  = $uid;
        $map['status']           = array('neq',4);
        if(I('get.real_name'))   $map['real_name'] = array('real_name','%'.I('get.title').'%');
        if(I('get.tel'))         $map['tel']       = I('get.tel');
        if(I('get.create_date'))
        {
            $b_time = strtotime(I('get.create_date'));
            $map['create_time'] = array('between',array($b_time,$b_time+86400));
        }

        if(I('get.id_str') && ($idAry=explode('-',I('get.id_str'))))  $map['id'] = array('in',$idAry);
        $list  = M('OfflineQudaoOrder')->where($map)->ORDER('id desc')->select();

        // 处理数据
        while (list($k,$v)=each($list)){
            $list[$k] = $this->disposeData($v);
        }
        $newList[0] = array(
            'order_sn'=>'对外订单编码',
            'goods_name'=>'订单商品',
            'goods_price'=>'商品价格',
            'goods_num'=>'购买数量',
            'create_date'=>'下单时间',
            'tel'=>'收货人电话',
            'real_name'=>'收货人',
            'address'=>'收货地址',
            'delivery_time'=>'配送时间',
            'delivery_note'=>'配送备注',
            'merge_status_info'=>'合并状态'
        );
        foreach ($list as $v) {
            $tmp = array();
            foreach ($newList[0] as $nk=>$nv) {
                $tmp[$nk] = trim(strip_tags($v[$nk]));
            }
            $newList[] = $tmp;
        }
        $this->excelData($newList,'订单列表','订单列表'.date('Y_m_d'),'订单列表'.date('Y_m_d'));
    }

    public function excelData($data,$titlename,$title,$filename){
        $str = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"\r\nxmlns:x=\"urn:schemas-microsoft-com:office:excel\"\r\nxmlns=\"http://www.w3.org/TR/REC-html40\">\r\n<head>\r\n<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">\r\n</head>\r\n<body>";
        $str .="<table border=1><head>".$titlename."</head>";
        $str .= $title;
        foreach ($data  as $key=> $rt )
        {
            $str .= "<tr>";
            foreach ( $rt as $k => $v )
            {
                $str .= "<td>{$v}</td>";
            }
            $str .= "</tr>\n";
        }
        $str .= "</table></body></html>";
        header( "Content-Type: application/vnd.ms-excel; name='excel'" );
        header( "Content-type: application/octet-stream" );
        header( "Content-Disposition: attachment; filename=".$filename.".xls" );
        header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header( "Expires: 0" );
        exit( $str );
    }

    /*生成订单号*/
    private function getOrderNo()
    {
        return "XX6".date('ymd').substr(time(),-5).substr(microtime(),2,5).rand(10,99);
    }

    /*获取地址*/
    public function getLoction()
    {
        $map = array();
        $parent_id      = I('get.parent_id');
        if($parent_id)  $map['parent_id'] = $parent_id;
        $provinceList = M('ZjisheLocation')->where($map)->select();
        echo json_encode($provinceList);
    }

    /*处理字段*/
    public function disposeData($arr)
    {
        $arr['delivery_time'] = substr($arr['delivery_time'],0,10);
        $arr['create_date']   = date('Y-m-d',$arr['create_time']);

        // 区域处理
        $zjs_loc_m = M('ZjisheLocation');
        if($arr['province']) {
            $arr['province_info'] = $zjs_loc_m->where('id='.$arr['province'])->getField('name');
        }
        if($arr['city']) {
            $arr['province_info']     = $zjs_loc_m->where('id='.$arr['city'])->getField('name');
        }
        if($arr['district']) {
            $arr['district_info'] = $zjs_loc_m->where('id='.$arr['district'])->getField('name');
        }

        // 合并状态
        $arr['merge_status_info'] = $arr['merge_status']==1 ? '已合并' : '待合并';

        // 地址详情
        $arr['address_info'] = $arr['province_info'].$arr['province_info'].$arr['district_info'].$arr['address'];
        return $arr;
    }
}