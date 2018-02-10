<?php
namespace Api\Controller;

use Think\Controller;
abstract class ApiController extends Controller
{
    /*初始化*/
    public function _initialize() {
        /*初始化客户端请求信息*/
        $server = I("server.");
          $this->client = array(
            "platform" => $server['HTTP_X_PLATFORM'],
            "verName" => $server['HTTP_X_VERNAME'],
            "verCode" => $server['HTTP_X_VERCODE'],
            "uuid" => $server['HTTP_X_UUID'],
            "device" => $server['HTTP_X_DEVICE'],
            "buildVersion" => $server['HTTP_X_BUILDVERSION'],
            "apiVersion" => $server['HTTP_X_API_VERSION'],
            "appType" => $server['HTTP_X_APP_TYPE'],   );
        /*验证接口签名*/
        //$this->checkSignature();
    }
    /*检查签名*/
    protected function checkSignature(){
      
       
       $str     = "Tv,cM02kjf^lWoU";
       $string  = '';
       //$version = I('get.version') ? I('get.version') : I('post.version');
       if(IS_GET){
           $data    = I('get.');
           ksort($data);
           foreach($data as $key=>$val){
            if(strcasecmp($key,"sign") != 0){
             $string.= $key."=".$val."&";    
            }
           }
           $string = substr($string, 0, -1);
           $md5 = md5($string.$str);
           //不区分大小写做判断
           if(strcasecmp($md5,I("get.sign")) != 0){
            
              $this->api_error(0002,'签名错误');
           }
       }
       if(IS_POST){
           $data    = I('post.');
           ksort($data);
           foreach($data as $key=>$val){
            if(strcasecmp($key,"sign") != 0){
             $string.= $key."=".$val."&";
            }
           }
           $string = substr($string, 0, -1);
           $md5 = md5($string.$str);
           if(strcasecmp($md5,I("post.sign")) != 0){
           $this->api_error(0002,'签名错误');
       }
     }

       
    }
      
           
     //验证客户端传过来的参数是否有问题
     protected function checkParam($paramData){
        foreach($paramData as $k=>$v){
            if($v == ''){
                $this->api_error(0001,'缺少必要参数'.$k);
            }
        }
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
