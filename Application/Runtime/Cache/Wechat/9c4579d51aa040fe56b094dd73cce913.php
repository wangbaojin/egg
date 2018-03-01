<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
	<meta charset="UTF-8">
	<script type="text/javascript" src="../../../../Public/js/Wechat/resize.js"></script>
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
			background: url(../../../../Public/images/Wechat/button.png) no-repeat center;
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
			background: url(../../../../Public/images/Wechat/bubble.png) no-repeat bottom;
	    	background-size: 70%;
			position: relative;
		}
		.sharediv div div{
			width: .8rem;
			height: 2.5rem;
			background: url(../../../../Public/images/Wechat/hand.png) no-repeat center;
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
</head>
<body>
	<div class="jiwoshare" v-title='"分享好友"'>
		<!--<div class="black">分享好友</div>-->
		<div class="jiwosharebox">
			<img src="../../../../Public/images/Wechat/background_01.png" width="100%">
			<img src="../../../../Public/images/Wechat/background_02.png" width="100%">
			<img src="../../../../Public/images/Wechat/background_03.png" width="100%">
			<img src="../../../../Public/images/Wechat/background_04.png" width="100%">
			<img src="../../../../Public/images/Wechat/background_05.png" width="100%">
			<img src="../../../../Public/images/Wechat/background_06.png" width="100%">
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
	</div>
</body>
<script type="text/javascript" src="../../../../Public/js/Wechat/zepto.min.js"></script>
<script type="text/javascript" charset="utf-8" async defer>
	$(".share").click(function (){     
        $(".sharediv").show();
    });
    $(".sharediv").click(function (){     
        $(".sharediv").hide();
    });
    $(".buy").click(function(){
	    location.href="buy.html";
	});
</script>
</html>