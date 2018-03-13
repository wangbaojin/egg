<?php
namespace WeChat\Controller;

use Think\Controller;
use Com\Wechat\WechatMsg;

class WeChatMsgController extends Controller
{

    /*-----------------微信认证------------------*/
    public function index()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($echoStr && $this->checkSignature()){
            echo $echoStr;
            exit;
        }

        $this->responseMsg();
    }

    //响应消息
    public function responseMsg()
    {
        //$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $m = new WechatMsg();
        $postStr = file_get_contents("php://input");
        if (!empty($postStr)){
            //$m->logger("R ".$postStr);
            $postObj = simplexml_load_string($postStr,'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            switch ($RX_TYPE)
            {
                case "event":
                    $result = $m->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $m->receiveText($postObj);
                    break;
                case "image":
                    $result = $m->receiveImage($postObj);
                    break;
                case "location":
                    $result = $m->receiveLocation($postObj);
                    break;
                case "voice":
                    $result = $m->receiveVoice($postObj);
                    break;
                case "video":
                    $result = $m->receiveVideo($postObj);
                    break;
                case "link":
                    $result = $m->receiveLink($postObj);
                    break;
                default:
                    $result = "unknown msg type: ".$RX_TYPE;
                    break;
            }
            //$this->logger("T ".$result);
            echo $result;
        }else {
            echo "";
            exit;
        }
    }
    private function checkSignature()
    {

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = '85ce0d6a319c09b564e2bd32cc64e38f';
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
    /*-----------------微信认证--end----------------*/
}