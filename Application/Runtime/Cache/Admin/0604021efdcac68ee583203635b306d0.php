<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>新建鸡舍</title>
	<style>
		*{
			margin: 0;
			padding: 0;
		}
		li{
			list-style-type:none;
		}
		.form-group label{
			line-height: 34px !important;
		}
		.col-sm-10>input{
			width: 500px !important;
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
		        <h5>新建鸡舍</h5>
		    </div>
			
			<form class="form-horizontal" id="create_form">

				<div class="form-group">
					<label><span style="color: red;">*</span>鸡舍ID</label>
					<div class="col-sm-10">
						{id123456}
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>鸡舍名称</label>
					<div class="col-sm-10">
						<input type="text" name="coop_name" id="coop_name" class="form-control" maxlength="11"  placeholder="请输入养殖场名称">
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>批次名称</label>
					<div class="col-sm-10">
						<input type="text" name="name" id="name" class="form-control" maxlength="11"  placeholder="批次名称">
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>鸡舍坐标</label>
					<div class="col-sm-10">
						<input type="text" name="latitude_longitude" id="latitude_longitude" class="form-control"   placeholder="请输入养殖场坐标">
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>蛋鸡品种</label>
					<div class="col-sm-10" style="display: flex;">
						<select type="text" name="breed" id="breed" class="form-control"  placeholder="品种" style="width:100px;margin-right: 10px;" >
							<option>海兰褐</option>
							<option>海兰灰</option>
							<option>海兰白</option>
							<option>罗曼褐</option>
							<option>罗曼粉</option>
							<option>伊莎</option>
							<option>京红</option>
							<option>京粉</option>
							<option>农大3号</option>
							<option>农大5号</option>
							<option>花凤</option>
							<option>金凤</option>
							<option>乌鸡</option>
							<option>其他</option>
						</select>
						<!-- <input type="text" name="breed" id="breed" class="form-control"   placeholder="品种"> -->
					</div>
				</div>
				<div class="form-group">
		            <label><span style="color: red;">*</span>入栏日期</label>
		            <div class="col-sm-10">
		            	<input type="datetime" name="lairage_date" id="lairage_date" onclick="layui.laydate({elem: this,value: new Date(), istime: true, format: 'YYYY-MM-DD'})" class="layui-input form-control" value="<?php echo ($delivery_date); ?>" placeholder="入栏日期">
		            </div>
		        </div>
				<div class="form-group">
					<label><span style="color: red;">*</span>入栏日龄</label>
					<div class="col-sm-10">
						<input type="number" name="age_in_days" id="age_in_days" class="form-control" maxlength="11"  placeholder="仅限整数1-720">
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>入栏数量</label>
					<div class="col-sm-10">
						<input type="number" name="chicken_num" id="chicken_num" class="form-control" maxlength="11"  placeholder="数量">
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>开放认购日龄</label>
					<div class="col-sm-10">
						<input type="number" name="start_times" id="start_times" class="form-control" maxlength="11"  placeholder="请务必填写入栏日龄">
						<input  type="hidden" name="start_time" id="start_time">
					</div>
					<div class="col-sm-10" style="width:510px;">
						<span id="start_date" style="display:inline-block;height: 30px;line-height: 30px;float: right;"></span>
						<!-- <input   id="start_date" class="form-control" style="display:inline-block;width: 100px;height: 30px; border: 0px;outline:none;-webkit-appearance: none;" readonly="readonly"> -->
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>截止认购日龄</label>
					<div class="col-sm-10">
						<input type="number" name="end_times" id="end_times" class="form-control" maxlength="11"  placeholder="请务必填写入栏日龄">
						<input  type="hidden" name="end_time" id="end_time">
					</div>
					<div class="col-sm-10" style="width:510px;">
						<span id="end_date"  style="display:inline-block;height: 30px;line-height: 30px;float: right;"></span>
						<!-- <input   id="end_date" class="form-control" style="display:inline-block;width: 100px;height: 30px; border: 0px;outline:none;-webkit-appearance: none;" readonly="readonly"> -->
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>开放认购数量</label>
					<div class="col-sm-10">
						<input type="number" name="amount" id="amount" class="form-control" maxlength="11"  placeholder="发行数量">
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>养殖方式</label>
					<div class="col-sm-10" style="display: flex;">
						<select type="text" name="feed_type" id="feed_type" class="form-control"  placeholder="养殖方式" style="width:200px;margin-right: 10px;" >
							<option>快乐的蛋定制养殖</option>
							<option>普通标准养殖</option>
							<option>无抗养殖</option>
						</select>
						<!-- <input type="text" name="feed_type" id="feed_type" class="form-control"   placeholder="养殖方式"> -->
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>产蛋颜色</label>
					<div class="col-sm-10" style="display: flex;">
						<select type="text" name="egg_color" id="egg_color" class="form-control"  placeholder=".input-lg" style="width:100px;margin-right: 10px;" >
							<option>红壳蛋</option>
							<option>粉壳蛋</option>
							<option>白壳蛋</option>
							<option>绿壳蛋</option>
						</select>
						<!-- <input type="text" name="egg_color" id="egg_color" class="form-control"   placeholder="产蛋颜色"> -->
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>预计产蛋</label>
					<div class="col-sm-10">
						<input type="number" name="lay_eggs" id="lay_eggs" class="form-control" maxlength="11"  placeholder="预计产蛋">
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>限购数量/人</label>
					<div class="col-sm-10">
						<select type="number"  class="form-control"  placeholder="" style="width:100px;margin-right: 10px;" >
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
						<!-- <input  type="text" name="title" id="title" class="form-control" placeholder="仅限整数1-10"> -->
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>认购价格/鸡</label>
					<div class="col-sm-10">
						<input type="number" name="chicken_price" id="chicken_price" class="form-control" maxlength="11"  placeholder="">
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>认购价格/套餐</label>
					<div class="col-sm-10">
						<input type="number" name="chicken_with_feed_price" id="chicken_with_feed_price" class="form-control" maxlength="11"  placeholder="">
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>蛋鸡编码</label>
					<div class="col-sm-10" style="display: flex;">
						<select type="text" name="out_code" MAXLENGTH="5" id="out_code" class="form-control"   placeholder="对外批次编码" style="width:100px;margin-right: 10px;" >
							<option>A</option>
							<option>B</option>
							<option>C</option>
							<option>D</option>
							<option>E</option>
							<option>F</option>
							<option>G</option>
							<option>H</option>
							<!-- <option>I</option> -->
							<option>J</option>
							<option>K</option>
							<option>L</option>
							<option>M</option>
							<option>N</option>
							<!-- <option>O</option> -->
							<option>P</option>
							<option>Q</option>
							<option>R</option>
							<option>S</option>
							<option>T</option>
							<option>U</option>
							<option>V</option>
							<option>W</option>
							<option>X</option>
							<option>Y</option>
							<option>Z</option>
						</select>
						<!-- <input type="text" name="out_code" MAXLENGTH="5" id="out_code" class="form-control"   placeholder="对外批次编码"> -->
					</div>
				</div>
				<div class="form-group">
					<label><span style="color: red;">*</span>app显示鸡场</label>
					<div class="col-sm-10">
						<!-- <input type="checkbox" name="top_num" id="top_num" class="" style="width: 20px;height: 20px;">
		            	<span>显示</span>
		            	<input type="checkbox" name="top_num" id="top_num" class="" style="width: 20px;height: 20px;">
		            	<span>不显示</span> -->
		            	<div  style="display: flex;">
			            	<input type="checkbox" name="is_default" id="is_default" style="width: 25px;height: 25px;margin-right: 10px;"><span style="line-height: 32px;">显示<small>[选择后当前发行批次会变为该添加批次]</small></span>
			            	<!-- <input type="checkbox" style="width: 25px;height: 25px;margin:5px 10px 0 20px;"><span style="line-height: 32px;">不显示</span> -->
			            </div>
					</div>

				</div>
				<button type="button" onclick="add()" id="tj" class="btn btn-default" style="width: 200px;height: 50px;">保存</button>
				<button type="button" onclick="clear()" id="clear" class="btn btn-default" style="width: 200px;height: 50px;">清空</button>



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
    function addDate(date,days){ 
	    var d=new Date(date); 
	    d.setDate(d.getDate()+days); 
	    var m=d.getMonth()+1; 
	    return d.getFullYear()+'-'+m+'-'+d.getDate(); 
	}
    $('#start_times').on('input',function(e) {
        var beginTimeVal=$('#lairage_date').val(),  //获取页面中的开始时间val（），格式为2017-01-11类型
            date='"'+beginTimeVal+'"', //拼接字符串【如果不拼接会被当做减法运算，传入的结果就为2005】
            days=$(this).val()-$('#age_in_days').val(),  //获取页面中的提前提醒时间（天）val（）
            daysInt=parseInt(days);//强制转换为int类型【不转换不会报错但是时间不准确】
        var val=addDate(date,daysInt);//调用已经封装好的函数addDate
        $('#start_date').html(val);
        $('#start_time').val(val);
    })
    $('#end_times').on('input',function(e) {
        var beginTimeVal=$('#lairage_date').val(),  //获取页面中的开始时间val（），格式为2017-01-11类型
            date='"'+beginTimeVal+'"', //拼接字符串【如果不拼接会被当做减法运算，传入的结果就为2005】
            days=$(this).val()-$('#age_in_days').val(),  //获取页面中的提前提醒时间（天）val（）
            daysInt=parseInt(days);//强制转换为int类型【不转换不会报错但是时间不准确】
        var val=addDate(date,daysInt);//调用已经封装好的函数addDate
        $('#end_date').html(val);
        $('#end_time').val(val);
    })

    function add(){


        url = "<?php echo U('AdminChickenBatch/add');?>"

        var formData = new FormData($( "#create_form" )[0]);

        coop_name     = $( "#coop_name" ).val();
        latitude_longitude    = $( "#latitude_longitude" ).val();
        breed    = $( "#breed" ).val();
        age_in_days    = $( "#age_in_days" ).val();
        lay_eggs    = $( "#lay_eggs" ).val();
        egg_color    = $( "#egg_color" ).val();
        feed_type    = $( "#feed_type" ).val();
        out_code    = $( "#out_code" ).val();
        name    = $( "#name" ).val();
        amount    = $( "#amount" ).val();
        start_time    = $( "#start_time" ).val();
        end_time    = $( "#end_time" ).val();
        lairage_date    = $( "#lairage_date" ).val();
        chicken_num    = $( "#chicken_num" ).val();
        chicken_price    = $( "#chicken_price" ).val();
        chicken_with_feed_price    = $( "#chicken_with_feed_price" ).val();
        if(!lairage_date || !out_code || !start_time || !end_time || !coop_name || !latitude_longitude || !breed || !age_in_days || !lay_eggs || !egg_color || !feed_type || !name || !amount || !chicken_price || !chicken_with_feed_price || !chicken_num) {
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
                    // setTimeout("parent.location.reload()", 2000);
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