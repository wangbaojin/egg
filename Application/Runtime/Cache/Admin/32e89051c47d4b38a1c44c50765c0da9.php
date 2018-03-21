<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>用户列表</title>
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
	<div class="layui-body">
		<!-- 内容主体区域 -->
		<!-- <iframe id="xuanxiang" name="xuanxiang" src="add.html" style="overflow: visible;" scrolling="no" frameborder="no" width="100%" height="100%"></iframe> -->
		<div id="option" name="option" style="overflow: visible;" scrolling="no" frameborder="no" width="100%" height="100%">
			<div class="jumbotron">
		        <h5>用户列表</h5>
		    </div>
			<form class="form-inline" name="searchForm" action="/AdminChickenBatch/index.html" method="get" >
		        手机号:<input  type="text" name="mobile" value="<?php echo ($_GET["mobile"]); ?>" style="width: 200px;height: 30px;margin: 0 40px 0 10px;">
		        昵称:<input  type="text" name="full_name" value="<?php echo ($_GET["full_name"]); ?>" style="width: 200px;height: 30px;margin: 0 40px 0 10px;">
		        ID:<input  type="text" name="id" value="<?php echo ($_GET["id"]); ?>" style="width: 200px;height: 30px;margin: 0 40px 0 10px;">
				<input  type="hidden" name="p" value="1">
				<input type="submit" class="btn btn-success " value="搜索" style="float: right;">
		    </form>
			<!-- <div id="page">共:<?php echo ($count); ?>条!<?php echo ($page); ?></div> -->
		    <div class="table-responsive" style="width: 100%;margin-top: 30px;">
		        <table class="table">
		            <tr style="border: 1px solid rgb(206,206,206);background: rgb(243,244,247);">
		                <th style="text-align: center;color: rgb(135,135,135);">批次名称</th>
		                <th style="color: rgb(135,135,135);width: 250px;">鸡舍信息</th>
		                <th style="text-align: center;color: rgb(135,135,135);">对外批次编码</th>
		                <th style="text-align: center;color: rgb(135,135,135);">发行数量</th>
		                <th style="text-align: center;color: rgb(135,135,135);">认购状态</th>
		                <th style="text-align: center;color: rgb(135,135,135);">发行开始时间</th>
		                <th style="text-align: center;color: rgb(135,135,135);">发行结束时间</th>
		                <th style="text-align: center;color: rgb(135,135,135);">是否是当前发行</th>
		                <th style="color: rgb(135,135,135);">操作</th>
		            </tr>
		            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr style="border: 1px solid rgb(206,206,206);">
		                    <td style="padding: 10px;text-align: center;line-height: 60px;">
		                        <?php echo ($vo["name"]); ?>
		                    </td>
		                    <td  style="padding: 10px;" >
		                        <?php echo ($vo["coop_name"]); ?><br>[<?php echo ($vo["breed"]); ?>]
		                        <?php echo ($vo["age_in_days"]); ?>天 <?php echo ($vo["egg_color"]); ?><br>预计产蛋:<?php echo ($vo["lay_eggs"]); ?><br><?php echo ($vo["feed_type"]); ?>
		                        <hr><?php echo ($vo["latitude_longitude"]); ?><small><?php echo ($vo["lairage_date"]); ?></small>
		                    </td>
		                    <td style="padding: 10px;text-align: center;line-height: 60px;">
		                        <?php echo ($vo["out_code"]); ?>
		                    </td>
		                    <td style="padding: 10px;text-align: center;line-height: 60px;">
		                        <?php echo ($vo["amount"]); ?>
		                    </td>
		                    <td style="padding: 10px;text-align: center;line-height: 60px;">
								<?php if(is_array($vo["chicken_state_info"])): $i = 0; $__LIST__ = $vo["chicken_state_info"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$chicken_state_info_vo): $mod = ($i % 2 );++$i; echo ($chicken_state_info_vo["state_info"]); ?>:<?php echo ($chicken_state_info_vo["count"]); ?><br><?php endforeach; endif; else: echo "" ;endif; ?>
		                    </td>
		                    <td style="padding: 10px;text-align: center;line-height: 60px;">
								<?php echo ($vo["start_date"]); ?>
		                    </td>
		                    <td style="padding: 10px;text-align: center;line-height: 60px;">
								<?php echo ($vo["end_date"]); ?>
		                    </td>
		                    <td  style="padding: 10px;text-align: center;line-height: 60px;">
								<?php echo ($vo["is_default_info"]); ?>
		                    </td>
		                    <td  style="padding: 10px;line-height: 60px;">
								<a href='<?php echo U("AdminChickenBatch/index");?>?id=<?php echo ($vo["id"]); ?>' class="btn btn-success btn-xs" target="_blank">编辑</a> &nbsp;
		                        <a href="<?php echo U('AdminChickenbatchTodayfeedDelivery/add');?>?chicken_batch=<?php echo ($vo["id"]); ?>">结算</a><br>
		                    </td>
		                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
		        </table>
		    </div>
		    <div id="page">共:<?php echo ($count); ?>条!<?php echo ($page); ?></div>
		</div>
	</div>
</body>
<script type="text/javascript" charset="utf-8">
	layui.use('layer', function(){
        var layer = layui.layer;
    });

    function edit(val) {
    	layer.open({
            type: 2,
            title: '编辑',
            shadeClose: false,
            maxmin: true,
            area: ['893px', '600px'],
            content: '<?php echo U("AdminUser/edit");?>?id='+val, //iframe的url
            cancel: function(index, layero){
                    layer.close(index)
                    location.reload();
                return false;
            }
        });
    }

    function changeSt(val) {


		layer.open({
			content: '是否加入黑名单',
			btn: ['确定', '取消'],
			yes: function(index, layero){
			//按钮【按钮一】的回调
			$.ajax({
		        type: 'GET',
		        url: '<?php echo U("AdminUser/changeSt");?>?id='+val, 
		        dataType: 'text',
		        success:function(msg){
		            if(msg=='success') {
		                layer.msg('成功!',{
		                    icon:1,
		                    time:2000
		                });
		                location.reload();
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
			},btn2: function(index, layero){
			//按钮【按钮二】的回调

			//return false 开启该代码可禁止点击该按钮关闭
			},cancel: function(){ 
			//右上角关闭回调

			//return false 开启该代码可禁止点击该按钮关闭
			}
		});
		
    }
</script>
</html>