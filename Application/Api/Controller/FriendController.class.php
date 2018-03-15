<?php
/**
 * Created by PhpStorm.
 * User: lkk
 * Date: 18/2/7
 * Time: 下午11:18
 */

namespace Api\Controller;

class FriendController extends ApiController
{
    /*添加*/
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

    /*列表*/
    public function getInviteBuyList()
    {
        $user_id = I('get.user_id');
        if (!$user_id) $this->api_error(20001, '请先登录');

        $m   = M('InviteBuy');
        $f_m = M('Friends');

        // 分页
        $page                = (int)I('page');

        // 条件
        $map                 = array();
        $map['user_id']      = $user_id;
        $data['page_limit']  = 20;
        $data['total_count'] = $f_m->where($map)->count();
        $data['total_page']  = ceil($data['total_count']/$data['page_limit']);
        $data['now_page']    = ($page > 0 and $page <= $data['total_page']) ? $page : 1;

        $list = $f_m->field('friend_id as user_id')->where($map)->page($page,$data['page_limit'])->order('id desc')->group('friend_id')->select();

        if(!$list) $this->api_error(20003,'暂无好友');

        $new_list = array();

        // 处理数据
        while (list($k,$v)=each($list))
        {
            $user_info = getUserInfoByUserId($v['user_id']);
            //if($user_id==2) print_r($user_info);
            if(empty($user_info['data']['mobile']) and empty($user_info['data']['wechart_info'])) continue;

            $v['user_pic']       = $user_info['data']['wechart_info']['wx_pic'];
            $v['user_full_name'] = $user_info['data']['wechart_info']['wx_nick_name'];

            if(M('Chicken')->where('user_id='.$v['user_id'])->find())
            {
                $v['buy_state'] = 1;
            }
            else
            {
                $invite_map['invite_user_id'] = $user_id;
                $invite_map['user_id']        = $v['user_id'];
                $invite_map['add_date']       = date('Y-m-d');
                $v['buy_state']               = $m->where($invite_map)->find() ? 2 : 3;
            }
            $new_list[] = $v;
        }
        $data['data']   = $new_list;
        $this->api_return('success',$data);
    }

    /*删除*/
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

    /*数据处理*/
    private function disposeData($arr)
    {
        return $arr;
    }
}