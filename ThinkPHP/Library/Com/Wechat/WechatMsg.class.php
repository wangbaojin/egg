<?php
namespace Com\Wechat;
/**
 * Created by PhpStorm.
 * User: lkk
 * Date: 18/2/24
 * Time: 下午1:30
 */
class WechatMsg {

    //检查签名
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = "weixin";
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if($tmpStr == $signature){
            return true;
        }else{
            return false;
        }
    }
    //查询自定义菜单
    public function getMenu(){
        $accessToken = json_decode($this->getToken(),1);
        $queryUrl = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$accessToken['access_token'];
        $str = file_get_contents($queryUrl);
        if ($str)
            return json_decode($str,1);
        return array('errcode' => '40013', 'errmsg' => 'Authentication failed');

    }
    //创建自定义菜单
    /**
     * 添加菜单，一级菜单最多3个，每个一级菜单最多可以有5个二级菜单
     * @param $menuList
     *          array(
     *              array('id'=>'', 'pid'=>'', 'name'=>'', 'type'=>'', 'code'=>''),
     *              array('id'=>'', 'pid'=>'', 'name'=>'', 'type'=>'', 'code'=>''),
     *              array('id'=>'', 'pid'=>'', 'name'=>'', 'type'=>'', 'code'=>''),
     *          );
     *          'code'是view类型的URL或者其他类型的key
     *          'type'是菜单类型，如下:
     *              1、click：点击推事件，用户点击click类型按钮后，微信服务器会通过消息接口推送消息类型为event的结构给开发者（参考消息接口指南），并且带上按钮中开发者填写的key值，开发者可以通过自定义的key值与用户进行交互；
     *              2、view：跳转URL，用户点击view类型按钮后，微信客户端将会打开开发者在按钮中填写的网页URL，可与网页授权获取用户基本信息接口结合，获得用户基本信息。
     *              3、scancode_push：扫码推事件，用户点击按钮后，微信客户端将调起扫一扫工具，完成扫码操作后显示扫描结果（如果是URL，将进入URL），且会将扫码的结果传给开发者，开发者可以下发消息。
     *              4、scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框，用户点击按钮后，微信客户端将调起扫一扫工具，完成扫码操作后，将扫码的结果传给开发者，同时收起扫一扫工具，然后弹出“消息接收中”提示框，随后可能会收到开发者下发的消息。
     *              5、pic_sysphoto：弹出系统拍照发图，用户点击按钮后，微信客户端将调起系统相机，完成拍照操作后，会将拍摄的相片发送给开发者，并推送事件给开发者，同时收起系统相机，随后可能会收到开发者下发的消息。
     *              6、pic_photo_or_album：弹出拍照或者相册发图，用户点击按钮后，微信客户端将弹出选择器供用户选择“拍照”或者“从手机相册选择”。用户选择后即走其他两种流程。
     *              7、pic_weixin：弹出微信相册发图器，用户点击按钮后，微信客户端将调起微信相册，完成选择操作后，将选择的相片发送给开发者的服务器，并推送事件给开发者，同时收起相册，随后可能会收到开发者下发的消息。
     *              8、location_select：弹出地理位置选择器，用户点击按钮后，微信客户端将调起地理位置选择工具，完成选择操作后，将选择的地理位置发送给开发者的服务器，同时收起位置选择工具，随后可能会收到开发者下发的消息。
     *
     * @return bool
     */
    public function creatMenu($menuList){
        //树形排布
        $menuList2 = $menuList;
        foreach($menuList as $key=>$menu){
            foreach($menuList2 as $k=>$menu2){
                if($menu['id'] == $menu2['pid']){
                    $menuList[$key]['sub_button'][] = $menu2;
                    unset($menuList[$k]);
                }
            }
        }
        //处理数据
        foreach($menuList as $key=>$menu){
            //处理type和code
            if(@$menu['type'] == 'view'){
                $menuList[$key]['url'] = $menu['code'];
                //处理URL。因为URL不能在转换JSON时被转为UNICODE
                $menuList[$key]['url'] = urlencode($menuList[$key]['url']);
            }else if(@$menu['type'] == 'click'){
                $menuList[$key]['key'] = urlencode($menu['code']);
            }else if(@!empty($menu['type'])){
                $menuList[$key]['key'] = urlencode($menu['code']);
                if(!isset($menu['sub_button'])) $menuList[$key]['sub_button'] = array();
            }
            unset($menuList[$key]['code']);
            //处理PID和ID
            unset($menuList[$key]['id']);
            unset($menuList[$key]['pid']);
            //处理名字。因为汉字不能在转换JSON时被转为UNICODE
            $menuList[$key]['name'] = urlencode($menu['name']);
            //处理子类菜单
            if(isset($menu['sub_button'])){
                unset($menuList[$key]['type']);
                foreach($menu['sub_button'] as $k=>$son){
                    //处理type和code
                    if($son['type'] == 'view'){
                        $menuList[$key]['sub_button'][$k]['url'] = $son['code'];
                        $menuList[$key]['sub_button'][$k]['url'] = urlencode($menuList[$key]['sub_button'][$k]['url']);
                    }else if($son['type'] == 'click'){
                        $menuList[$key]['sub_button'][$k]['key'] = urlencode($son['code']);
                    }else{
                        $menuList[$key]['sub_button'][$k]['key'] = urlencode($son['code']);
                        $menuList[$key]['sub_button'][$k]['sub_button'] = array();
                    }
                    unset($menuList[$key]['sub_button'][$k]['code']);
                    //处理PID和ID
                    unset($menuList[$key]['sub_button'][$k]['id']);
                    unset($menuList[$key]['sub_button'][$k]['pid']);
                    unset($menuList[$key]['key']);
                    //处理名字。因为汉字不能在转换JSON时被转为UNICODE
                    $menuList[$key]['sub_button'][$k]['name'] = urlencode($son['name']);
                }
            }
        }
        //整理格式
        $data = array();
        $menuList = array_values($menuList);
        $data['button'] = $menuList;
        //转换成JSON
        $data = json_encode($data);
        $data = urldecode($data);
        //print_r($data);die;
        //获取ACCESS_TOKEN
        $accessToken = json_decode($this->getToken(),1);
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$accessToken['access_token'];
        $result =$this->http_request($url, $data);
        if($result['errcode'] == 0){
            return true;
        }
        return $result;
    }

    //响应消息
    public function responseMsg()
    {
        //$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postStr = file_get_contents("php://input");
        if (!empty($postStr)){
            //$this->logger("R ".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
                case "image":
                    $result = $this->receiveImage($postObj);
                    break;
                case "location":
                    $result = $this->receiveLocation($postObj);
                    break;
                case "voice":
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video":
                    $result = $this->receiveVideo($postObj);
                    break;
                case "link":
                    $result = $this->receiveLink($postObj);
                    break;
                default:
                    $result = "unknown msg type: ".$RX_TYPE;
                    break;
            }
            //$this->logger("T ".$result);
            if(!$result) $this->receiveUnKnow($postObj);
            echo $result;
        }else {
            echo "";
            exit;
        }
    }

    //  未知消息回复
    public function receiveUnKnow($postObj)
    {
        $result = $this->transmitText($postObj, '您的消息我们已经收到，处理完毕后会及时跟您联系。');
        return $result;
    }

    //接收事件消息
    public function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            case "subscribe":
                $result = $this->transmitText($object, "欢迎关注链养鸡!请大家给微信公众号【链养鸡】发送您的钱包地址，每人随机可获得空投若干EggCoin（2018-03-15 0:00~2018-03-16 24:00）");
                break;
            case "unsubscribe":
                $content = "取消关注";
                break;
            case "CLICK":
                $result =$this->receiveClickEvent($object);
                break;
            default :
                break;
        }
        /*if(is_array($content)){
            if (isset($content[0])){
                $result = $this->transmitNews($object, $content);
            }
            if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }*/

        return $result;
    }

    //点击事件
    public function receiveClickEvent($object)
    {
        switch ($object->EventKey)
        {
            case "jrhd":
                $result = $this->transmitText($object, '请大家给微信公众号【链养鸡】发送您的钱包地址，每人随机可获得空投若干EggCoin（2018-03-15 0:00~2018-03-16 24:00）');
                break;
            case "wxjq":
                $result = $this->transmitImage($object,array('MediaId'=>'016AZGlp1I7FQuvAXVtrAHEZDAbWzJtGcRA4jTM7gMM'));
                //$result = $this->transmitText($object, 'Telegram');
                break;
            case "lx":
                $result = $this->transmitText($object, 'Telegram：@lianyangji
微信：wwwlianyangjiio
微信公众号：lianyangjiio
QQ:22472073
QQ 群：711399308');
                break;
            default :
                break;
        }
        return $result;
    }
    //接收文本消息
    public function receiveText($object)
    {
        switch ($object->Content)
        {
            case "文本":
                $content = "这是个文本消息";
                break;
            case "今日活动":
                $content = "活动还未开始,请留意公众号信息噢!~";
                break;
            case "图文":
            case "单图文":
                $content = array();
                $content[] = array("Title"=>"单图文标题",  "Description"=>"单图文内容", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                break;
            case "多图文":
                $content = array();
                $content[] = array("Title"=>"多图文1标题", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                $content[] = array("Title"=>"多图文2标题", "Description"=>"", "PicUrl"=>"http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                $content[] = array("Title"=>"多图文3标题", "Description"=>"", "PicUrl"=>"http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                break;
            case "音乐":
                $content = array("Title"=>"最炫民族风", "Description"=>"歌手：凤凰传奇", "MusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3", "HQMusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3");
                break;
            default:
                $content = '您的消息我们已经收到，处理完毕后会及时跟您联系。';
                break;
        }
        if(is_array($content)){
            if (isset($content[0]['PicUrl'])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }

    //接收图片消息
    public function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    //接收位置消息
    public function receiveLocation($object)
    {
        $content = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收语音消息
    public function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }

        return $result;
    }

    //接收视频消息
    public function receiveVideo($object)
    {
        $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
        $result = $this->transmitVideo($object, $content);
        return $result;
    }

    //接收链接消息
    public function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //回复文本消息
    private function transmitText($object, $content)
    {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //回复图片消息
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
                        <MediaId><![CDATA[%s]]></MediaId>
                    </Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[image]]></MsgType>
                    $item_str
                    </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复语音消息
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
                        <MediaId><![CDATA[%s]]></MediaId>
                    </Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);

        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[voice]]></MsgType>
                    $item_str
                    </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复视频消息
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
                        <MediaId><![CDATA[%s]]></MediaId>
                        <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                    </Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[video]]></MsgType>
                    $item_str
                    </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "<item>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                </item>";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $newsTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <Content><![CDATA[]]></Content>
                    <ArticleCount>%s</ArticleCount>
                    <Articles>
                    $item_str</Articles>
                    </xml>";

        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }

    //回复音乐消息
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <MusicUrl><![CDATA[%s]]></MusicUrl>
                        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                    </Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[music]]></MsgType>
                    $item_str
                    </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //日志记录
    private function logger($log_content)
    {
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
            $max_size = 10000;
            $log_filename = "log.xml";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);
        }
    }

    //curl
    private function http_request($url,$post) {
        #1.初始化curl
        $ch = curl_init();
        #2.设置请求地址
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //终端不验证curl
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //模拟 POST
        if(!empty($post)){
            curl_setopt($ch,CURLOPT_POST,1);//模拟post
            curl_setopt($ch,CURLOPT_POSTFIELDS,$post);//post内容
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json'
        ));
        #3.执行curl
        $Token_Outopt = curl_exec($ch);
        #4.关闭curl
        curl_close($ch);
        #5.格式化数据
        $access_arr = json_decode($Token_Outopt,true);
        #6.返回值
        return $access_arr;
    }
}