<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
	<meta charset="UTF-8">
	<script type="text/javascript" src="/Public/js/Wechat/resize.js"></script>
	<title>微信支付</title>

</head>
<body>
</body>
<script type="text/javascript" src="/Public/js/Wechat/jquery-3.2.1.min.js"></script>
<script src="/Public/static/layui/layui.js"></script>
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script type="text/javascript">
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
			link: 'http://wechat.jiagehao.cn/ShareByChicken?invite_code=<?php echo ($invite_code); ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
			imgUrl: 'http://wechat.jiagehao.cn/Public/images/Api/share_icon.png', // 分享图标
			success: function () {
				// 用户确认分享后执行的回调函数
				//
			}
		});
		wx.onMenuShareAppMessage({
			title: '我养了只鸡，每天都能赚钱，还免费领鸡蛋币', // 分享标题
			desc: '不养狗，不养猫，不骗人；邀请您区块链真实养鸡，领取透明收益，数字资产记账。', // 分享描述
			link: 'http://wechat.jiagehao.cn/ShareByChicken?invite_code=<?php echo ($invite_code); ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
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
		jssdkdata = eval('('+'<?php echo ($jssdkdata); ?>'+')')
		//var ss = parseInt(jssdkdata.timeStamp)
		wx.chooseWXPay({
			timestamp: jssdkdata.timeStamp, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
			 nonceStr: jssdkdata.nonceStr, // 支付签名随机串，不长于 32 位
			 package: jssdkdata.package, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）
			 signType: jssdkdata.signType, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
			 paySign: jssdkdata.paySign, // 支付签名*/
			/*
			timestamp: 1520427000, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
			nonceStr: 'gXwLUHKAbQkotKz4', // 支付签名随机串，不长于 32 位
			package: 'prepay_id=wx20180307205000fed17c6b0a0810548465', // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）
			signType: 'MD5', // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
			paySign: 'EB2F054911C1DAB0669DB2B290656CC4', // 支付签名*/
			success: function (pay_success_res) {
				// 支付成功后的回调函数
				//alert('认养成功!快去链养鸡APP获取收益吧!')
				layer.msg('认养成功!快去链养鸡APP获取收益吧!', {
					icon: 1,
					time:2000,
				});
				setTimeout("location.href='http://a.app.qq.com/o/simple.jsp?pkgname=io.jiwo.lianyangji'",2001);
			},fail: function(pay_error_res){
				alert(JSON.stringify(pay_error_res));
				return false;
			},cancel: function () {
				// 用户取消分享后执行的回调函数
				layer.msg('您取消了支付!', {
					icon: 2,
					time:2000,
				});
			}
		});
	});
</script>
</html>