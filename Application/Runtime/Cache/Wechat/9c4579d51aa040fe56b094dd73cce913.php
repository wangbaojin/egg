<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
	<meta charset="UTF-8">
	<script type="text/javascript" src="/Public/js/Wechat/resize.js"></script>
	<title>分享好友</title>
	<style>
		*{
			margin: 0;
			padding: 0;
		}
		html{
			font-size: 100px;
		}
		li{
			list-style-type:none;
		}
		.jiwoshare{
			width: 100%;
			height: 100%;
		}
		.black{
			width: 100%;
			height: 1.28rem;
			font-size: .5rem;
			text-align: center;
			line-height: 1.28rem;
		}
		.jiwosharebox{
			width: 100%;
			height: 12.06rem;
			position: relative;
		}
		.jiwosharebox img{
		    outline-width: 0px;
    		vertical-align: top;
		}
		
	    .background img{
	        vertical-align: top;
	        display: block;
	    }
		.bg{
	        /*width: 100%;*/
	        height: 8rem;
	        background:#ffca74;
	        margin-top: -1px;
			padding-top: 1rem;
	    }
		.bg div{
			/*width: 100%;*/
			height: 1rem;
			font-size: .3rem;
			color: #d44900;
			padding: 0 5%;
			display: flex;
		}
		.bg div b{
			width: 35%;
			height: 1px;
			background: #d44900;
			margin-top: .5rem;
		}
		.bg div i{
			display: inline-block;
			width: 30%;
			line-height: 1rem;
			text-align: center;
		}
		.bg p{
			font-size: .3rem;
			color: #d44900;
			padding: 0 8%;
			/*width: 100%;*/
			line-height: .5rem;
		}
		.bg span{
			font-size: .3rem;
			color: #d44900;
			padding: 0 3% 0 8%;
			height: 1rem;
			float: left;
			line-height: .5rem;
		}
		.button{
			width: 100%;
			height: 1rem;
			position: absolute;
			top: 8.5rem;
			font-size: .36rem;
			color: #fff;
			display: flex;
		}
		.button div{
			width: 50%;
	    	height: 1rem;
			background: url(/Public/images/Wechat/button.png) no-repeat center;
	    	background-size: 80%;
			line-height: 1rem;
	    	text-align: center;
		}
		.sharediv{
			width: 100%;
			height: 100%;
			background: #000;
			position: fixed;
			top: 0;
			opacity: 0.7;
			z-index: 10;
			display: none;
		}
		.sharediv div{
			width: 100%;
			height: 6rem;
			background: url(/Public/images/Wechat/bubble.png) no-repeat bottom;
	    	background-size: 70%;
			position: relative;
		}
		.sharediv div div{
			width: .8rem;
			height: 2.5rem;
			background: url(/Public/images/Wechat/hand.png) no-repeat center;
	    	background-size: 100%;
			position: absolute;
			top: 0;
			right: .5rem;
		}
		.sharediv div p{
			width: 60%;
			height: 1.2rem;
			padding: 0 20%;
			font-size: .4rem;
			color: #ffca74;
			position: absolute;
			text-align: center;
			line-height: .6rem;
			bottom: 1rem;
			z-index: 12;
		}
	</style>
	<style type="text/css">  
        .scroll{  
        position:absolute;   
        overflow:scroll;   
        -webkit-overflow-scrolling: touch;   
        top:0;   
        left:0;   
        bottom:0;   
        right:0;  
    }
    </style> 
</head>
<body><div class="scroll">
	<div class="jiwoshare" v-title='"分享好友"'>

		<!--<div class="black">分享好友</div>-->
		<div class="jiwosharebox">
			<img src="/Public/images/Wechat/background_01.png" width="100%">
			<img src="/Public/images/Wechat/background_02.png" width="100%">
			<img src="/Public/images/Wechat/background_03.png" width="100%">
			<img src="/Public/images/Wechat/background_04.png" width="100%">
			<img src="/Public/images/Wechat/background_05.png" width="100%">
			<img src="/Public/images/Wechat/background_06.png" width="100%">
			<div class="bg">
				<div>
					<b></b>
					<i>认养规则</i>
					<b></b>
				</div>
				<p>✪认养蛋鸡后，根据实际鸡蛋收益，将收益打入您的收益账户；</p>
				<p>✪根据您的鸡蛋收益比例，下发EggCoin数字资产；</p>
				<p>✪本邀请为内测VIP权限邀请，自动获得永久VIP权限；</p>
				<p>✪VIP拥有提前认购资格及正在规划中的其它权益；</p>
				<p>✪认养投资可以于本批次蛋鸡出栏后申请退回，内测用户仅扣取支付渠道手续费0.6%;</p>
				<p>✪其它未尽事宜，请咨询客服</p>
				<!--<p><span>✪</span><p>认养蛋鸡后，根据实际鸡蛋收益，将收益打入您的收益账户；</p></p>
				<p><span>✪</span><p>根据您的鸡蛋收益比例，下发EggCoin数字资产；</p></p>
				<p><span>✪</span><p>本邀请为内测VIP权限邀请，自动获得永久VIP权限；</p></p>
				<p><span>✪</span><p>VIP拥有提前认购资格及正在规划中的其它权益；</p></p>
				<p><span>✪</span><p>认养投资可以于本批次蛋鸡出栏后申请退回，内测用户仅扣取支付渠道手续费0.6%;</p></p>
				<p><span>✪</span><p>其它未尽事宜，请咨询客服</p></p>-->
			</div>
			<div class="button">
				<div class="share">分享好友</div>
				<div class="buy">我也要认领</div>
			</div>
			<!--分享弹窗-->
			<div class="sharediv" v-if="sharediv" @click="jiwoshare()">
				<div>
					<div></div>
					<p>邀请好友得额外奖励哦</p>
				</div>
			</div>
		</div>
	</div></div>
</body>
<script type="text/javascript" src="/Public/js/Wechat/jquery-3.2.1.min.js"></script>
<script src="/Public/static/layui/layui.js"></script>
<script src="/Public/js/Wechat/jquery.cookie.js"></script>
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script type="text/javascript">
	 //禁止下拉  
    var overscroll = function(el) {  
	  el.addEventListener('touchstart', function() {  
	    var top = el.scrollTop  
	      , totalScroll = el.scrollHeight  
	      , currentScroll = top + el.offsetHeight  
	    //If we're at the top or the bottom of the containers  
	    //scroll, push up or down one pixel.  
	    //  
	    //this prevents the scroll from "passing through" to  
	    //the body.  
	    if(top === 0) {  
	      el.scrollTop = 1  
	    } else if(currentScroll === totalScroll) {  
	      el.scrollTop = top - 1  
	    }  
	  })  
	  el.addEventListener('touchmove', function(evt) {  
	    //if the content is actually scrollable, i.e. the content is long enough  
	    //that scrolling can occur  
	    if(el.offsetHeight < el.scrollHeight)  
	      evt._isScroller = true  
	  })  
	}  
	overscroll(document.querySelector('.scroll'));  
	document.body.addEventListener('touchmove', function(evt) {  
	  //In this case, the default behavior is scrolling the body, which  
	  //would result in an overflow.  Since we don't want that, we preventDefault.  
	  if(!evt._isScroller) {  
	    evt.preventDefault()  
	  }  
	})  
	$(document).ready(function(){
		function stopScrolling(event){
			event.preventDefault();
		}
		document.addEventListener('touchmove','stopScrolling',false)
	})
	layui.use('layer', function(){ //独立版的layer无需执行这一句
		//var $ = layui.jquery,
		layer = layui.layer; //独立版的layer无需执行这一句
	});
    ticket = eval('('+'<?php echo ($ticket); ?>'+')')
    wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: ticket.appId, // 必填，公众号的唯一标识
        timestamp: ticket.timestamp, // 必填，生成签名的时间戳
        nonceStr: ticket.nonceStr, // 必填，生成签名的随机串
        signature: ticket.signature,// 必填，签名
        jsApiList: [
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'chooseWXPay',
        ] // 必填，需要使用的JS接口列表
    });
    wx.ready(function(){
        // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
        wx.onMenuShareTimeline({
            title: '我养了只鸡，每天都能赚钱，还免费领鸡蛋币', // 分享标题
            link: 'http://wechat.jiagehao.cn/ShareByChicken?invite_code=<?php echo ($self_invite_code); ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://wechat.jiagehao.cn/Public/images/Api/share_icon.png', // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
                //
                //alert('success')
            }
        });
		wx.onMenuShareAppMessage({
			title: '我养了只鸡，每天都能赚钱，还免费领鸡蛋币', // 分享标题
			desc: '不养狗，不养猫，不骗人；邀请您区块链真实养鸡，领取透明收益，数字资产记账。', // 分享描述
			link: 'http://wechat.jiagehao.cn/ShareByChicken?invite_code=<?php echo ($self_invite_code); ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
			imgUrl: 'http://wechat.jiagehao.cn/Public/images/Api/share_icon.png', // 分享图标
			type: 'link', // 分享类型,music、video或link，不填默认为link
			dataUrl: 'share_icon.png', // 如果type是music或video，则要提供数据链接，默认为空
			success: function () {
				// 用户确认分享后执行的回调函数
			},
			cancel: function () {
				// 用户取消分享后执行的回调函数
			}
		});
    });
	wx.error(function(res){
		//alert(res.err_msg);
	});
	$(".share").click(function (){
        $(".sharediv").show();
    });
    $(".sharediv").click(function (){     
        $(".sharediv").hide();
    });
    $(".buy").click(function(){
		$.cookie('invite_code','');
    	buy_url = "<?php echo U('ShareByChicken/buy');?>";
		invite_code = "<?php echo ($invite_code); ?>"
		if(invite_code) {
			buy_url = buy_url+"?invite_code="+invite_code
			$.cookie('invite_code',invite_code,300);
		}
	    location.href=buy_url;
	});
</script>
</html>