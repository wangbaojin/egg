<?php
/**
 * Created by PhpStorm.
 * User: lkk
 * Date: 18/2/7
 * Time: 下午11:18
 */

namespace Api\Controller;

class NewsController extends ApiController
{
    private $_come_from = array(
        1 => '微信',
        2 => '微博',
    );

    private $_not_null = array(
        'title'       => '文字标题都不填,你是不是飘了',
        'come_from'   => '饮水思源,来个文章来源呗',
        'article_url' => '文章链接都没有,你让我跳一跳?',
        'newstime'    => '选个发布时间吧,操碎了心',
        'abstract'    => '文章摘要填下,不要皮',
    );


    private $_pic_path = './Public/images/uploads/news';


    /*今日价格*/
    public function todayPrice()
    {
        $price  = M('TodayPrice')->order('delivery_date')->limit(1)->find();
        if(!$price) $this->api_error(20001,'今日价格还未公布');
        $price['updated_date'] = date('Y-m-d H:i:s',$price['updated_at']);
        $data['data'] = $price;
        $this->api_return('success',$data);
    }

    /*新闻录入*/
    public function add()
    {
        $data = I('post.');
        if(!$data)
        {

            $this->api_error(0,'数据不能为空');
        }

        foreach ($this->_not_null as $k=>$v)
        {
            if(!$data[$k]) $this->api_error(0,$v);
        }

        $res = M('News')->add($data);
        if($res)
        {
            $msg = '添加成功!';

            // 上传商品图片
            if(!$_FILES)
            {
                $up_res = $this->uploadsImg($res);
                if($up_res['err_no']==1)
                {
                    foreach($up_res['info'] as $fk=>$file)
                    {
                        if($fk=='news_cover') $change_data['news_cover']  = $file['savepath'].$file['savename'];
                    }
                    $change_res = M('News')->where('id='.$res)->save($change_data);
                }
                else
                {
                    $msg .= $up_res['err_msg'];
                }
            }
            $this->api_return($msg);
        }
        else
        {
            $this->api_error( 0 , '添加失败');
        }
    }

    /*上传图片*/
    private function uploadsImg($savePath)
    {

        $data = array();
        if(!$_FILES)
        {
            $data['err_no']     = 2;
            $data['err_msg']    = '没有文件上传';
        }
        else
        {
            $rootPath = $this->_pic_path;
            if(!is_dir($rootPath)) mkdir($rootPath);
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     10240000;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =     $rootPath; // 设置附件上传根目录
            $upload->autoSub   =     false;
            //$upload->savePath  =      '/'.$savePath.'/'; // 设置附件上传（子）目录
            $upload->savePath  =      '/'; // 设置附件上传（子）目录
            $upload->saveName  =      array('uniqid',$savePath.'_');

            // 上传文件
            $info   =   $upload->upload();
            if(!$info)
            {// 上传错误提示错误信息
                $data['err_no']     = 3;
                $data['err_msg']    = $upload->getError();
            }
            else
            {// 上传成功 获取上传文件信息
                $data['err_no']     = 1;
                $data['err_msg']    = 'success';
                $data['info']       = $info;
            }
        }
        return $data;
    }

    /*新闻列表*/
    public function getList()
    {
        $m = M('News');
        $page = (int)I('page');

        // 分页
        $map['dis_st']  = 1;
        $map['top_num'] = array('eq',0);

        // 第一页的时候记录用户阅读记录
        $user_id = I('get.user_id');
        if($user_id && $page==1)
        {
            $readed_m = M('NewsReaded');
            $readed_info = $readed_m->where('user_id='.$user_id)->find();
            $last_id  = $m->order('id desc')->limit(1)->getField('id');
            if($readed_info)
            {
                $readed_m->where('user_id='.$user_id)->setField('news_id',$last_id);
            }
            else
            {
                $readed_data['user_id'] = $user_id;
                $readed_data['news_id'] = $last_id;
                $readed_m->add($readed_data);
            }
        }

        $data['page_limit'] = 20;
        $data['total_count'] = $m->where($map)->count();
        $data['total_page'] = ceil($data['total_count'] / $data['page_limit']);
        $data['now_page'] = ($page > 0 and $page <= $data['total_page']) ? $page : 1;
        $date_list = $m->where($map)->page($page, $data['page_limit'])->group('newstime_date')->order('newstime_date desc')->getField('newstime_date', true);
        if (!$date_list) $this->api_error(20003, '暂无收益');

        // 处理列表
        foreach ($date_list as $dk => $dv) {
            $tmp_map = $f_tmp = array();
            $tmp_map['newstime_date'] = $dv;
            $tmp_map['top_num'] = array('eq',0);
            $tmp_list = $m->where($tmp_map)->select();
            if (!$tmp_list) continue;
            foreach ($tmp_list as $k => $v) {
                $f_tmp['info_list'][] = $this->disposeData($v);
            }
            $f_tmp['newstime_date'] = $dv;
            $data['data'][] = $f_tmp;
        }
        $this->api_return('success', $data);
    }

    /*置顶新闻列表*/
    public function getTopList()
    {
        $m = M('News');

        // 分页
        $map['top_num'] = array('neq',0);
        $data['total_count'] = $m->where($map)->count();
        $list = $m->where($map)->order('id desc')->select();

        if(!$list) $this->api_error(20003,'暂无');

        // 第一页的时候记录用户阅读记录
        $user_id = I('get.user_id');
        if($user_id)
        {
            $readed_m = M('NewsReaded');
            $readed_info = $readed_m->where('user_id='.$user_id)->find();
            $last_id  = $m->order('id desc')->limit(1)->getField('id');
            if($readed_info)
            {
                $readed_m->where('user_id='.$user_id)->setField('news_id',$last_id);
            }
            else
            {
                $readed_data['user_id'] = $user_id;
                $readed_data['news_id'] = $last_id;
                $readed_m->add($readed_data);
            }
        }

        // 处理数据
        while (list($k,$v)=each($list))
        {
            $list[$k] = $this->disposeData($v);
        }
        $data['data'] = $list;
        $this->api_return('success',$data);
    }

    /*删除新闻*/
    public function del()
    {
        $id = I('get.id');
        if(!$id) $this->api_error(0,'选择个删除的新闻!');

        if(M('News')->delete())
        {
            $this->api_return('删除成功!');
        }
        $this->api_error(0,'删除失败!');
    }

    /*获取来源*/
    public function getComeFrom()
    {
        $this->api_return('success',$this->_come_from);
    }

    /*数据处理*/
    private function disposeData($arr)
    {
        /*创建时间*/
        $arr['create_date'] = date('Y-m-d',$arr['create_time']);

        /*发布时间*/
        $arr['newstime_date'] = date('Y-m-d',$arr['newstime']);

        /*图片*/
        $arr['news_cover'] = $arr['news_cover'] ? getSelf().$this->_pic_path.$arr['news_cover'] : '';

        /*来源*/
        if($arr['come_from']) $arr['come_from_info'] = C('NEWS_COME_FROM')[$arr['come_from']];

        /*置顶*/
        if($arr['top_num']==1) $arr['top_num_info'] = '置顶🔝';

        return $arr;
    }
}