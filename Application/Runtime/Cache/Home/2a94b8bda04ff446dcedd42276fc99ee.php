<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>链养鸡—区块链实体养鸡</title>
	<style>
		*{
			margin: 0;
			padding: 0;
		}
		li{
			list-style-type:none;
		}
		.nav{
		    width: 100%;
    		height: 50px;
		    background: #333;
		}
		.nav img{
			width: 32px;
		    height: 32px;
		    margin-left: 20%;
		    margin-bottom: 9px;
		}
		.nav ul{
			display: inline-block;
			margin-left: 20%;
		}
		.nav li{
		    display: inline-block;
		    width: 100px;
		    height: 50px;
		    text-align: center;
		    line-height: 50px;
			float: left;
			color: #fff;
		}
		.banner{
			width: 100%;
			height: 400px;
			background: url(/Public/images/Api/banner.jpg);
		}
		.banner div{
		    width: 100%;
			height: 80px;
		    text-align: center;
		    font-size: 24px;
			color: #fff;
			padding-top: 150px;
		}
		.banner p:nth-of-type(1){
			font-size: 50px;
		}
		.main{
			width: 80%;
			height: 100px;
		    box-shadow: 5px 5px 5px #999;
		    padding: 0 10%;
		    position: relative;
		}
		.main ul{
			width: 100%;
			height: 100px;
		}
		.main li{
		    display: inline-block;
		    width: 25%;
		    height: 100px;
		    text-align: center;
		    line-height: 100px;
			float: left;
		}
		.main li:hover{
			color: #f7ab26;
		}
		.downs{
			width: 150px;
		    height: 165px;
		    position: absolute;
		    left: 55%;
		    top: -125px;
		    background: url(/Public/images/Api/walletbg.png) no-repeat bottom;
		    text-align: center;
	        display: none;	
		}
		.downs img{
			width: 130px;
    		height: 130px;
    		margin-top: 5px;
		}
		.wechats{
			width: 150px;
		    height: 165px;
		    position: absolute;
		    left: 35%;
		    top: -125px;
		    background: url(/Public/images/Api/walletbg.png) no-repeat bottom;
		    text-align: center;
	        display: none;	
		}
		.wechats img{
			width: 120px;
    		height: 120px;
    		margin-top: 10px;
		}
		.down:hover .downs{
			display: block;
		}
		.body{
			width: 70%;
			height: 120px;
		    margin: 50px 15%;
		    margin-top: 100px;
	        position: relative;
		}
		.body ul{
			display: inline-block;
		    float: left;
	        line-height: 120px;
		}
		.body a{
			text-decoration: none;
			color: #999;
			font-size: 12px;
		}
		.body li{
			display: inline-block;
		    height: 120px;
			float: left;
			margin-right: 100px;
		}
		.body li p:nth-of-type(1){
			color: #000;
			font-weight: 700;
			margin-bottom: 10px;
		}
		.bodyright{
			width: 40%;
			height: 120px;
	        position: absolute;
	        right: 0;
		}
		.left{
			position: absolute;
    		right: 130px;
    		top: 30px;
		}
		.right{
			position: absolute;
    		right: 0;
		}
		.bodyright img{
			width: 110px;
			height: 110px;
		}
		.line{
			width: 70%;
			height: 1px;
			background: #999;
			margin: 0 15%;
		}
		.right p{
			font-size: 12px;
			text-align: center;	
		}
		.left p:nth-of-type(2){
			font-size: 24px;
		}
		.footer{
			text-align: center;
			width: 80%;
			padding: 0 10%;
			font-size: 12px;
			color: #999;
			margin-top: 50px;
		}
		.footer a{
			display: inline-block;
			width: 100px;
			text-align: center;
			border-left: 1px solid #999;
			text-decoration: none;
			color: #999;
		}

		a:hover{color: skyblue;}
	</style>
</head>
<body>
	<div class="nav">
		<img src="/Public/images/Api/icon.png">
		<ul>
		    <li></li>
		    <li>首页</li>
		    <li></li>
		    <li></li>
		</ul>
	</div>
	<div class="banner">
		<div>
			<p>链养鸡</p>
			<p>区块链实体养鸡 透明收益</p>
		</div>
	</div>
	<div class="main">
		<ul>
		    <a href="http://api.lianyangji.io/Index/introduce" style="color: #000;"><li>产品介绍</li></a>
		    <li class="wechat">关注微信</li>
		    <li class="down">下载软件</li>
		    <a href="https://support.qq.com/product/22998" style="color: #000;"><li>联系我们</li></a>
		</ul>
		<div class="wechats">
			<img src="/Public/images/Api/weixin.png">
		</div>
		<div class="downs">
			<img src="/Public/images/Api/QR.png">
		</div>
	</div>
	<div class="body">
		<ul>
		    <li>
		    	<p><a href="">关于我们</a></p>
		    	<!-- <p><a href="">网心科技</a></p>
		    	<p><a href="">公司简介</a></p>
		    	<p><a href="">发展历程</a></p> -->
		    </li>
		    <li>
		    	<p><a href="">媒体中心</a></p>
		    	<!-- <p><a href="">新闻动态</a></p>
		    	<p><a href="">精彩活动</a></p>
		    	<p><a href="">最新公告</a></p> -->
		    </li><li>
		    	<p><a href="">服务支持</a></p>
		    	<!-- <p><a href="">常见问题</a></p>
		    	<p><a href="">售后服务</a></p>
		    	<p><a href="">联系我们</a></p> -->
		    </li><li>
		    	<p><a href="">关注我们</a></p>
		    	<!-- <p><a href="">官方微博</a></p>
		    	<p><a href="">官方微信</a></p>
		    	<p><a href="">官方社区</a></p> -->
		    </li>
		</ul>
		<div class="bodyright">
			<div class="right">
	    		<img src="/Public/images/Api/QR.png">
	    		<p>扫码下载链养鸡APP</p>
	    	</div>
	    	<div class="left">
	    		<p>服务时间：10:00-19:00</p>
	    		<p>010-57105035</p>
	    	</div>
	    	
	    </div>
	    
	</div>
	<div class="line"></div>
	<div class="footer">
		<span>Copyright © 2018 lianyangji.io All Rights Reserved</span>
		<a href="">隱私條款</a>
		<a href="">法律聲明</a>
		<a href="">使用者協議</a>
		<a href="">網站地圖</a>
	</div>
</body>
<script type="text/javascript" src="/Public/js/Wechat/jquery-3.2.1.min.js"></script>
<script type="text/javascript" charset="utf-8" async defer>
    $(".down").mouseover(function (){  
		$(".downs").show();  
	}).mouseout(function (){  
		$(".downs").hide();  
	});
	$(".downs").mouseover(function (){  
		$(".downs").show();  
	}).mouseout(function (){  
		$(".downs").hide();  
	});
	$(".wechat").mouseover(function (){  
		$(".wechats").show();  
	}).mouseout(function (){  
		$(".wechats").hide();  
	});
	$(".wechats").mouseover(function (){  
		$(".wechats").show();  
	}).mouseout(function (){  
		$(".wechats").hide();  
	});
	
</script>
</html>