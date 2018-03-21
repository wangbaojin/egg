<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>发币发钱</title>
	<style>
		*{
			margin: 0;
			padding: 0;
		}
		li{
			list-style-type:none;
		}
		.col-sm-10{
			display: flex;
    		align-items: center;
		}
		.col-sm-10 span{
			margin-left: 10px !important;
		}
		.col-sm-10 input{
			width: 500px !important;
		}
		/*.form-group{
			width: 600px !important;
		}*/
		.form-group label{
			line-height: 34px !important;
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
				<dd><a href="javascript:;">鸡舍列表</a></dd>
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
		        <h5>发币发钱</h5>
		    </div>

			<form class="form-horizontal" id="create_form">

				<div class="form-group">
					<label ><span style="color: red;">*</span>结算鸡场</label>
					<div class="col-sm-10">
						<select type="text" name="chicken_batch" id="chicken_batch" class="form-control" style="width:200px;margin-right: 10px;">
							<option value="">--请选择--</option>
			                <?php if(is_array($chicken_batch)): $i = 0; $__LIST__ = $chicken_batch;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if($vo['id'] == $_GET['chicken_batch']): ?>selected<?php endif; ?>>--<?php echo ($vo["name"]); ?>--</option><?php endforeach; endif; else: echo "" ;endif; ?>
							<!-- <option>批次鸡场1</option>
							<option>批次鸡场2</option>
							<option>批次鸡场3</option>
							<option>批次鸡场4</option>
							<option>批次鸡场5</option> -->
						</select>
					</div>
				</div>
				<div class="form-group">
		            <label><span style="color: red;">*</span>结算日期<?php if($chicken_batch['guess_age_in_days']): ?>今日日龄提示:<?php echo ($guess_age_in_days); endif; ?></label>
		            <div class="col-sm-10">
            			<input type="datetime" name="delivery_date" id="delivery_date" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD'})" class="layui-input form-control" value="<?php echo ($delivery_date); ?>" placeholder="开始时间">
		            </div>
		        </div>
		        <div class="form-group">
					<label><span style="color: red;">*</span>当日蛋价</label>
					<div class="col-sm-10">
						<input  type="text" name="today_egg_price" id="today_egg_price" class="form-control" placeholder="请看清是公斤价格">
						<span>元/kg</span>
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>当日饲料价格</label>
					<div class="col-sm-10">
						<input  type="text" name="today_feed_price" id="today_feed_price" class="form-control" placeholder="请看清是公斤价格">
						<span>元/kg</span>
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>当日总死淘数</label>
					<div class="col-sm-10">
						<select type="number" name="death" id="death" class="form-control"  style="width:200px;margin-right: 10px;" >
							<option>0</option>
							<option>1</option>
							<option>2</option>
							<option>3</option>
							<option>4</option>
							<option>5</option>
							<option>6</option>
							<option>7</option>
							<option>8</option>
							<option>9</option>
							<option>10</option>
							<option>11</option>
							<option>12</option>
							<option>13</option>
							<option>14</option>
							<option>15</option>
							<option>16</option>
							<option>17</option>
							<option>18</option>
							<option>19</option>
							<option>20</option>
						</select>
						<span>羽</span>
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>当日投料重量</label>
					<div class="col-sm-10">
						<input  type="number" name="feed_weight" id="feed_weight" class="form-control"   placeholder="请输入总投料数">
						<span>kg</span>
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>当日总产蛋数</label>
					<div class="col-sm-10">
						<input  type="number" name="lay_eggs" id="lay_eggs" class="form-control" maxlength="11"  placeholder="产蛋数量">
						<span>枚</span>
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>当日产蛋重量</label>
					<div class="col-sm-10">
						<input type="number" name="lay_eggs_weight" id="lay_eggs_weight" class="form-control"   placeholder="总蛋重">
						<span>kg</span>
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>当日其他支出</label>
					<div class="col-sm-10">
						<input  type="number" name="expenses" id="expenses" class="form-control"   placeholder="现金支出">
						<span>元</span>
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>当日发token/鸡</label>
					<div class="col-sm-10">
						<input  type="number" name="eggcoin_income" id="eggcoin_income" class="form-control"   placeholder="数字币">
						<span>Eggcoin</span>
					</div>
				</div>

		        <button type="button" onclick="add()" id="tj" class="btn btn-default" style="width: 200px;height: 50px;">保存</button>
		        <button type="button" onclick="clear()" id="tj" class="btn btn-default" style="width: 200px;height: 50px;">清空</button>
			</form>
		</div>
</body>
<script type="text/javascript" charset="utf-8">

	layui.use('layer', function(){ //独立版的layer无需执行这一句
        //var $ = layui.jquery,
        layer = layui.layer; //独立版的layer无需执行这一句
    });
    layui.use('laydate', function(){
        var laydate = layui.laydate;
        var start = {
            min: laydate.now(),
            istoday: true,
            choose: function(datas){
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        };
    });
    function add(){


        url = "<?php echo U('AdminChickenbatchTodayfeedDelivery/add');?>"

        var formData = new FormData($( "#create_form" )[0]);

        chicken_batch     = $( "#chicken_batch" ).val();
        lay_eggs    = $( "#lay_eggs" ).val();
        lay_eggs_weight    = $( "#lay_eggs_weight" ).val();
        death    = $( "#death" ).val();
        feed_weight    = $( "#feed_weight" ).val();
        expenses    = $( "#expenses" ).val();
        delivery_date    = $( "#delivery_date" ).val();
        eggcoin_income    = $( "#eggcoin_income" ).val();
        today_egg_price    = $( "#today_egg_price" ).val();
        today_feed_price    = $( "#today_feed_price" ).val();

        if(!eggcoin_income || !chicken_batch || !lay_eggs || !lay_eggs_weight || !death || !feed_weight || !expenses || !delivery_date || !today_egg_price || !today_feed_price) {
            layer.msg('程序员小哥哥提示你:信息都不填全就提交?是我拿不动刀了还是你飘了?',{
                icon:2,
                time:2000
            });return false;
        }

        $('#tj').attr('disabled',true)
        $('#tj').html('正在提交。。。')

        $.ajax({
            type: 'POST',
            url: url,
            data:formData,
            processData: false,
            contentType: false,
            dataType: 'text',
            success:function(msg){
                if(msg=='success') {
                    layer.msg('添加成功!',{
                        icon:1,
                        time:2000
                    });
                    $('#tj').html('提交')
                    $('#tj').attr('disabled',false)
                    setTimeout("parent.location.reload()", 2000);
                }
                else
                {
                    layer.msg(msg,{
                        icon:2,
                        time:2000
                    });
                    $('#tj').html('提交')
                    $('#tj').attr('disabled',false)
                }
            },
            error:function(){
                layer.msg('程序员小哥哥提示你:未知错误!请稍后重试!',{
                    icon:2,
                    time:2000
                });
                $('#tj').html('提交')
                $('#tj').attr('disabled',false)
            }
        });
    }
</script>
</html>