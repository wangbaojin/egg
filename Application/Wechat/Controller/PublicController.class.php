<?php
namespace Wechat\Controller;

use Think\Controller;
abstract class PublicController extends Controller
{
    /*初始化*/
    public function _initialize() {

    }

    /**
     * API错误输出
     * @param $code
     * @param $message
     */
    protected function api_error($error,$message){
        $error = array(
            'code'    => 0,
            'error' => $error,
            'msg' => $message,
        );
        $this->ajaxReturn($error,'JSON');
    }

    /**
     * API数据返回
     * @param $data $data可以为空数据
     */
    protected function api_return($message,$data=array()){
        $result = array(
            'code'    => 1,
            'msg' => $message,
            'result' => $data
        );
        $this->ajaxReturn($result,'JSON');
    }
}
