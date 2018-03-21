<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>新闻详情</title>
	<style>
		*{
			margin: 0;
			padding: 0;
		}
		li{
			list-style-type:none;
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
		        <h5>编辑新闻</h5>
		    </div>

			<form class="form-horizontal" id="create_form">
				<div class="form-group">
					<label>文章来源</label>
					<div class="col-sm-10"  style="display: flex;">
						<!-- <?php if(is_array($come_from)): $i = 0; $__LIST__ = $come_from;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$c_vo): $mod = ($i % 2 );++$i; echo ($c_vo); ?><input type="radio" <?php if($info['come_from'] == $key): ?>checked<?php endif; ?> name="come_from" value="<?php echo ($key); ?>"><?php endforeach; endif; else: echo "" ;endif; ?> -->
						<select type="text" name="come_from" id="c_vo" class="form-control" style="width:150px;margin-right: 10px;">
							<option>--请选择--</option>
							<?php if(is_array($come_from)): $i = 0; $__LIST__ = $come_from;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$c_vo): $mod = ($i % 2 );++$i;?><option <?php if($info['come_from'] == $key): ?>selected<?php endif; ?> value="<?php echo ($key); ?>" ><?php echo ($c_vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
							<!-- <option>微信公众号</option>
							<option>微博</option>
							<option>腾讯新闻</option>
							<option>网易新闻</option>
							<option>今日头条</option>
							<option>其他</option> -->
						</select>
					</div>
				</div>

				<div class="form-group">
					<label >图片</label>
					<div class="col-sm-10">
						<input type="file" type="file" name="news_cover" id="news_cover" value="<?php echo ($info["news_cover"]); ?>">
						<p class="help-block">750*200分辨率的图片，格式png，jpeg，jpg,大小不超过3M</p>
						<img src="<?php echo ($info["news_cover"]); ?>" width="100px" alt="">
					</div>
				</div>
				<div class="form-group">
					<label>文章H5链接</label>
					<div class="col-sm-10" style="display: flex;">
						<select type="text" name="url_title" id="url_title"  value="<?php echo ($info["url_title"]); ?>" class="form-control" style="width:100px;margin-right: 10px;">
							<option>https://</option>
							<option>http://</option>
						</select>
						<input type="text" name="article_url" id="article_url" value="<?php echo ($info["article_url"]); ?>" class="form-control" placeholder="请填写新闻采编链接">
					</div>
					<div class="col-sm-10" style="margin-left: 100px;">
						<p class="help-block">优先选用Https:// 链接，否则iOS图片有可能不显示</p>
					</div>
				</div>
				<div class="form-group">
					<label>新闻标题</label>
					<div class="col-sm-10">
						<input  type="text" name="title" id="title" value="<?php echo ($info["title"]); ?>" class="form-control" placeholder="请填写活动标题">
					</div>
				</div>
				<div class="form-group">
					<label>新闻摘要</label>
					<div class="col-sm-10" style="height: 100px;">
						<input name="abstract" id="abstract" value="<?php echo ($info["abstract"]); ?>" class="form-control"  placeholder="请编写摘要" style="height: 100px">
					</div>  
				</div>
				<div class="form-group">
		            <label>发布时间:</label>
		            <div class="col-sm-10">
		            	<input type="datetime" name="newstime" id="newstime" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD 00:00:00'})" class="layui-input form-control" value="<?php echo ($info["newstime_date"]); ?>" placeholder="发布时间">
		            </div>
		        </div>
		        <div class="form-group">
		            <label>是否置顶:</label>
		            <div class="col-sm-10">
		            	<input type="checkbox" style="width: 20px;height: 20px;" <?php if($info['top_num'] == 1): ?>checked<?php endif; ?> name="top_num" id="top_num" class="" >
		            	<span>[选择后该文章将会置顶]</span>
		            </div>
		        </div>
		        <button type="button" onclick="edit()" id="tj" class="btn btn-default" style="width: 200px;height: 50px;">保存并发布</button>
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
	function edit(){


        url = "<?php echo U('AdminNews/edit');?>"+"<?php echo ($info["id"]); ?>"

        var formData = new FormData($( "#create_form" )[0]);

        title       = $( "#title" ).val();
        article_url = $( "#url_title" ).val() + $( "#article_url" ).val();
        newstime = $( "#newstime" ).val();
        top_num  = $( "#top_num" ).val();
        abstract = $( "#abstract" ).val();

        if(!title || !article_url || !newstime || !top_num || !abstract) {
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