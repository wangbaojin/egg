<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>用户详情</title>
	<style>
		*{
			margin: 0;
			padding: 0;
		}
		li{
			list-style-type:none;
		}
		#option{
			padding: 20px;
		}
		.col-sm-10{
			display: flex; 
			width: 100%;
			height: 40px;
			align-items: center;
			border: 1px solid #999;
			border-top: 0;
		}
	</style>
</head>
<body>
	
	<div class="layui-body">
		<!-- 内容主体区域 -->
		<!-- <iframe id="xuanxiang" name="xuanxiang" src="add.html" style="overflow: visible;" scrolling="no" frameborder="no" width="100%" height="100%"></iframe> -->
		<div id="option" name="option" style="overflow: visible;" scrolling="no" frameborder="no" width="100%" height="100%">
			
			<div class="jumbotron">
		        <h5>用户详情</h5> 
		    </div>
			<div  style="display: flex;justify-content: space-between;align-items: center;width: 100%;height: 40px;border: 1px solid #999;padding: 0 15px;">
				<div>用户ID<?php echo ($user_id); ?></div>
				<div>注册时间<?php echo ($date_time); ?></div>
			</div>
			<div style="border: 1px solid #999;border-top: 0; display: flex;">
				<span style="margin: 10px 50px 10px 10px;">头像</span>
				<img src="<?php echo ($vo["user_info"]["pic"]); ?>" alt="" style="width:100px;height: 100px;margin: 10px;background: red;">
				<span style="margin: auto;" >{VIP}</span>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">微信昵称</p><p class="text-left">{}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">手机号</p><p class="text-left">{18612347674}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">认养鸡数</p><p class="text-left">{5羽}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">鸡蛋收益</p><p class="text-left">{120.34元}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">已累计提现</p><p class="text-left">{210.90元}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">已累计充值</p><p class="text-left">{1678.01元}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">当前饲料余额</p><p class="text-left">{12333g}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">当前欠款</p><p class="text-left">{4.775元}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">当前欠饲料</p><p class="text-left">{12275g}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">生蛋发token</p><p class="text-left">{780}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">邀请好友数</p><p class="text-left">{3人}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">邀请发token</p><p class="text-left">{3}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">手机系统</p><p class="text-left">{IOS}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">软件版本号</p><p class="text-left">{v1.0}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">提现支付宝账户</p><p class="text-left">{未知}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">用户邮箱</p><p class="text-left">{未知}</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" style="width: 30%;">最近登录时间</p><p class="text-left">{2018-12-31 19:09:08}</p>
			</div>

		</div>
</body>
<script type="text/javascript" charset="utf-8">

</script>
</html>