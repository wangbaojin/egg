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
			width: 100% !important;
			height: 40px;
			align-items: center;
			border: 1px solid rgb(206,206,206);
			border-top: 0;
		}
		.text-left{
			width: 30%;
		}
	</style>
</head>
<body>
	<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<script type="text/javascript" src="/Public/js/Wechat/jquery-3.2.1.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<link rel="stylesheet" type="text/css" href="/Public/static/layui/css/layui.css">
	<script src="/Public/static/layui/layui.js"></script>

	<title>链养鸡后台管理系统</title>
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
		.form-group{
			margin:20px 0 !important;
		}
		.form-group label{
			float:left !important;
			width: 110px !important;
		}
		#page {
			padding: 20px;
		}
		#page .num{
		    display: inline-block;
			font-size: 18px;
			width: 40px;
			height: 30px;
			color: #01AAED;
			line-height: 30px;
			text-align: center;
			border: 1px solid #ddd;
		}
		#page .current{
			display: inline-block;
			font-size: 18px;
			width: 40px;
			height: 30px;
			color: #000;
			line-height: 30px;
			text-align: center;
			border: 1px solid #ddd;
		}
		dd>a{
			font-size: 12px ;
			margin-left: 10px ;
		}
	</style>
</head>
<body>
	<ul class="layui-nav layui-nav-tree" lay-filter="test">
		<!-- 侧边导航: <ul class="layui-nav layui-nav-tree layui-nav-side"> -->
		<li class="layui-nav-item"><a href="">首页</a></li>
		<li class="layui-nav-item "><!-- layui-nav-itemed 移出默认展开-->
			<a href="javascript:;">用户管理</a>
			<dl class="layui-nav-child">
				<dd><a href="<?php echo U('AdminUser/index');?>">用户列表</a></dd>
				<!-- <dd><a href="javascript:;">用户详情</a></dd> -->
			</dl>
		</li>
		<li class="layui-nav-item">
			<a href="javascript:;">鸡舍管理</a>
			<dl class="layui-nav-child">
				<dd><a href="<?php echo U('AdminChickenBatch/index');?>">鸡舍列表</a></dd>
				<dd><a href="<?php echo U('AdminChickenBatch/add');?>">新建鸡舍</a></dd>
				<dd><a href="<?php echo U('AdminChickenbatchTodayfeedDelivery/add');?>">发币发钱</a></dd>
				<dd><a href="javascript:;">帖子评论</a></dd>
			</dl>
		</li>
		<li class="layui-nav-item">
			<a href="javascript:;">通知新闻</a>
			<dl class="layui-nav-child">
				<dd><a href="<?php echo U('AdminNews/index');?>">新闻列表</a></dd>
				<dd><a href="<?php echo U('AdminNews/add');?>">新建新闻</a></dd>
			</dl>
		</li>
		<li class="layui-nav-item">
			<a href="javascript:;">活动</a>
			<dl class="layui-nav-child">
				<dd><a href="<?php echo U('AdminAdvertisement/index');?>">活动列表</a></dd>
				<dd><a href="<?php echo U('AdminAdvertisement/add');?>">新建活动</a></dd>
			</dl>
		</li>
		<li class="layui-nav-item">
			<a href="javascript:;">提现管理</a>
			<dl class="layui-nav-child">
				<dd><a href="<?php echo U('AdminWithdrawals/index');?>">提现列表</a></dd>
			</dl>
		</li>
	</ul>
	<div class="layui-body">
		<!-- 内容主体区域 -->
		<!-- <iframe id="xuanxiang" name="xuanxiang" src="add.html" style="overflow: visible;" scrolling="no" frameborder="no" width="100%" height="100%"></iframe> -->
		<!-- <div id="option" name="option" style="overflow: visible;" scrolling="no" frameborder="no" width="100%" height="100%"></div> -->
	</div>
</body>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script type="text/javascript" charset="utf-8">
	layui.use('element', function(){
		var element = layui.element(); //导航的hover效果、二级菜单等功能，需要依赖element模块

		//监听导航点击
		element.on('nav(demo)', function(elem){
		//console.log(elem)
		layer.msg(elem.text());
		});
	});
	layui.use('layer', function(){
		var layer = layui.layer;
	});
</script>
</html>
	<div class="layui-body" >
		<!-- 内容主体区域 -->
		<!-- <iframe id="xuanxiang" name="xuanxiang" src="add.html" style="overflow: visible;" scrolling="no" frameborder="no" width="100%" height="100%"></iframe> -->
		<div id="option" name="option" style="overflow: visible; margin-bottom: 200px;" scrolling="no" frameborder="no" width="100%" height="100%">
			
			<div class="jumbotron">
		        <h5>用户详情</h5> 
		    </div>
			<div  style="display: flex;justify-content: space-between;align-items: center;width: 100%;height: 40px;border: 1px solid rgb(206,206,206); background: rgb(243,244,247);padding: 0 15px;">
				<div>用户ID<?php echo ($info["id"]); ?></div>
				<div>注册时间<?php echo ($info["created_date"]); ?></div>
			</div>
			<div style="border: 1px solid rgb(206,206,206);border-top: 0; display: flex;width: 100%;padding: 0 15px;">
				<span style="width: 30%;">头像</span>
				<img src="<?php echo ($info["wechart_info"]["wx_pic"]); ?>" alt="" style="width:100px;height: 100px;margin: 10px;background: red;">
				<span style="margin: auto;" ><?php echo ($info["vip_info"]); ?></span>
			</div>
			<div class="col-sm-10">
				<p class="text-left" >微信昵称</p><p class="text-left"><?php echo ($info["full_name"]); ?></p><p class="btn btn-default btn-xs" onclick="changeMobile(<?php echo ($vo["id"]); ?>)">修改</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" >手机号</p><p class="text-left"><?php if($info['mobile']): echo ($info["mobile"]); else: ?>暂未绑定<?php endif; ?></p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" >认养鸡数</p><p class="text-left"><?php echo ($info["buy_chicken_num"]); ?>只</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" >鸡蛋收益</p><p class="text-left"><?php echo ($info["wallet"]["amount"]); ?>元</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" >已累计提现</p><p class="text-left"><?php echo ($info["withdrawals"]); ?>元</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" >已累计充值</p><p class="text-left"><?php echo ($info["recharge"]); ?>元</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" >当前饲料余额</p><p class="text-left"><?php echo ($info["wallet"]["feed_amount"]); ?>kg</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" >当前欠款</p><p class="text-left"><?php echo ($info["wallet"]["arrears_amount"]); ?>元</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" >生蛋发token</p><p class="text-left"><?php echo ($info["chick_eggcoin"]); ?>枚</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" >邀请好友数<small>[成功购买数/总数]</small></p><p class="text-left"><?php echo ($info["invite_success_buy"]); ?>人 / <?php echo ($info["invite_buy"]); ?> 人</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" >邀请发token</p><p class="text-left"><?php echo ($info["invite_eggcoin"]); ?>枚</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" >手机系统</p><p class="text-left">未知</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" >软件版本号</p><p class="text-left">未知</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" >提现支付宝账户</p><p class="text-left">
					<?php if($info['zfb_list']): if(is_array($info["zfb_list"])): $i = 0; $__LIST__ = $info["zfb_list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$zfb_vo): $mod = ($i % 2 );++$i; echo ($zfb_vo); ?><br><?php endforeach; endif; else: echo "" ;endif; ?>
					<?php else: ?>
						暂无<?php endif; ?>
				</p>
			</div>
			<div class="col-sm-10">
				<p class="text-left" >用户邮箱</p><p class="text-left"><?php echo ($info["email"]); ?></p>
			</div>
			<div class="col-sm-10" style="margin-bottom: 200px;">
				<p class="text-left" >最近登录时间</p><p class="text-left"><?php echo ($info["updated_date"]); ?></p>
			</div>

		</div>
	</div>
</body>
<script type="text/javascript" charset="utf-8">
	function changeMobile(val) {

		layer.prompt(function(value, index, elem){
			// alert(value); //得到value
			
			$.ajax({
		        type: 'GET',
		        url: '<?php echo U("AdminUser/edit");?>?id='+val+'&mobile='+value, 
		        dataType: 'text',
		        success:function(msg){
		            if(msg=='success') {
		                layer.msg('成功!',{
		                    icon:1,
		                    time:2000
		                });
		                layer.close(index);
		                setTimeout(location.reload(),2000)
		                
		            }
		            else
		            {
		                layer.msg(msg,{
		                    icon:2,
		                    time:2000
		                });
		            }
		        },
		        error:function(){
		            layer.msg('程序员小哥哥提示你:未知错误!请稍后重试!',{
		                icon:2,
		                time:2000
		            });
		        }
		    });

		});
		
	}
</script>
</html>