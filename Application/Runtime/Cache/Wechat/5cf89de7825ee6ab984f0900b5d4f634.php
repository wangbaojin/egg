<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
	<meta charset="UTF-8">
	<meta http-equiv="Access-Control-Allow-Origin" content="*" />

	<script type="text/javascript" src="/Public/js/Wechat/resize.js"></script>
	<script type="text/javascript" src="../js/resize.js"></script>
	<title>订单信息</title>
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
		.order{
			width: 100%;
			height: 100%;
			background: #f2f2f2;
		    position: absolute;
		}
		.black{
			width: 100%;
			height: 1.28rem;
			font-size: .5rem;
			text-align: center;
			line-height: 1.28rem;
		}
		.orderbox{
			width: 95%;
			/*height: 12.06rem;*/
			position: relative;
			background: #fff;
			margin: 2.5%;
			border-radius: 5px;
			padding: .3rem 0;
		}
		.orderbox p{
			width: 100%;
			height: .8rem;
			display: flex;
			justify-content: space-between;
		}
		.orderbox p span:nth-of-type(1){
			font-size: .3rem;
			line-height: .8rem;
			height: .8rem;
			margin-left: .3rem;
			color: #6b6b6b;
		}
		.orderbox p span:nth-of-type(2){
			font-size: .3rem;
			line-height: .8rem;
			height: .8rem;
			margin-right: .3rem;
			color: #6b6b6b;
		}
		.buynum{
			width: 100%;
			height: 1rem;
			display: flex;
			line-height: 1rem;
			font-size: .3rem;
			color: #6b6b6b;
		}
		.buynum>span{
			margin-left: .3rem;
		}

		#addegg ul li .comSelectCont>div span{
			width: 30%;
			line-height: .7rem;
			text-align: center;
			font-size: .36rem;
			color: #4E4E4E;
			/*border-bottom: 1px solid #E7E7E7;
			border-top: 1px solid #E7E7E7;*/
		}
		.addcount{
			width: 2.4rem;
			height: .8rem;
			position: absolute;
			right: .3rem;
			display: flex;
		    margin-top: .1rem;
		}
		.addcount input{
			width: .8rem;
			line-height: .8rem;
			text-align: center;
			font-size: .36rem;
			color: #6173ae;
			border: none;
			border-bottom: 1px solid #E7E7E7;
			border-top: 1px solid #E7E7E7;
		}
    	.btnminus{
			display: inline-block;
			text-align: center;
			background-color: #E7E7E7;
			color: #A7A7A7;
			border-radius: .1rem 0 0 .1rem;
			width: .8rem;
			height: .8rem;
			font-size: .4rem;
			line-height: .8rem;
		}
		.btnadd{
			display: inline-block;
			text-align: center;
			background-color: #E7E7E7;
			color: #A7A7A7;
			border-radius:  0 .1rem .1rem 0;
			width: .8rem;
			height: .8rem;
			font-size: .4rem;
			line-height: .8rem;
		}
    	.wechatpay{
			width: 100%;
			height: .88rem;
			position: fixed;
			bottom: 0;
			display: flex;
			background: #fff;
    	}
    	.total{
			width: 65%;
			height: .88rem;
			font-size: .36rem;
			display: flex;
			justify-content: flex-end;
    	}
    	.total input{
			width: 1.5rem;
			line-height: .88rem;
			text-align: left;
			font-size: .36rem;
			color: #ff0000;
			border: none;
		}
    	.total span:nth-of-type(1){
			line-height: .88rem;
			color: #6b6b6b;
		}
		.total span:nth-of-type(2){
			line-height: .88rem;
			color: #ff0000;
			margin-left: .2rem;
		}
    	.pay{
			width: 35%;
			height: .88rem;
			text-align: center;
			color: #fff;
			background: #ffa200;
			font-size: .36rem;
			line-height: .88rem;
    	}
	</style>
</head>
<body>
	<div class="order" v-title='"订单信息"'>
		<!--<div class="black">认购母鸡</div>-->
		<div class="orderbox">
			<p>
				<span>订单编号</span>
				<span class="order_sn">VIP20180307000121</span>
				<span class="chicken_batch" style="display: none"></span>
			</p>
			<p>
				<span>养殖场名称</span>
				<span class="coop_name">养鸡专业户</span>
			</p>
			<p>
				<span>鸡场坐标</span>
				<span class="latitude_longitude">116.307629,40.058359</span>
			</p>
			<p>
				<span>蛋鸡品种</span>
				<span class="breed">海兰褐</span>
			</p>
			<p>
				<span>当前日龄</span>
				<span class="age_in_days">150天</span>
			</p> 
			<p>
				<span>预计产蛋</span>
				<span class="lay_eggs">280~320</span>
			</p>
			<p>
				<span>产蛋颜色</span>
				<span class="egg_color">红壳蛋</span>
			</p>
			<p>
				<span>养殖方式</span>
				<span class="feed_type">无抗养殖</span>
			</p>
            <div class="buynum">
            	<span>认购数量</span>
            	<div class="addcount">
            		<span class="btnminus">-</span>
					<input type="text" class="ipt" value="1" readonly="readonly">
					<span class="btnadd">+</span>
            	</div>
            </div>
		</div>
        <div class="wechatpay">
        	<div class="total">
        		<span>合计：</span>
        		<span>￥</span>
        		<input type="number" class="paynum" value="0" readonly="readonly">
        	</div>
        	<div class="pay">去支付</div>
        </div>
	</div>
</body>
<script type="text/javascript" src="/Public/js/Wechat/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="../js/jquery-3.2.1.min.js"></script>
<script src="/Public/js/Wechat/jquery.cookie.js"></script>
<script src="/Public/static/layui/layui.js"></script>
<script type="text/javascript">
	layui.use('layer', function(){ //独立版的layer无需执行这一句
		//var $ = layui.jquery,
		layer = layui.layer; //独立版的layer无需执行这一句
	});
    $.ajax({
        type: "get",
        url: "http://wechat.jiagehao.cn/ShareByChicken/buyChicken",
        // url:"./data.json",
        dataType: "json",
        success: function(data){
         	console.log(data);
			var chicken_batch=data.result.data.id
			$(".chicken_batch").html(chicken_batch)
         	 order_sn = data.result.data.order_sn
         	$(".order_sn").html(order_sn)
         	var coop_name = data.result.data.coop_name
         	$(".coop_name").html(coop_name)
         	var latitude_longitude = data.result.data.latitude_longitude
         	$(".latitude_longitude").html(latitude_longitude)
         	var breed = data.result.data.breed
         	$(".breed").html(breed)
         	var age_in_days = data.result.data.age_in_days
         	$(".age_in_days").html(age_in_days)
         	var lay_eggs = data.result.data.lay_eggs
         	$(".lay_eggs").html(lay_eggs)
         	var egg_color = data.result.data.egg_color
         	$(".egg_color").html(egg_color)
         	var feed_type = data.result.data.feed_type
         	$(".feed_type").html(feed_type)
         	var cookieid=$.cookie('chicken_type');
         	console.log(cookieid)
         	
         	switch(cookieid){
         		case "1":
         			$(".paynum").val(168)
         			price=168
         			break;
         		case "2":
					$(".paynum").val(29.9)
					price=29.9
					break;
         	}
		}
    });
	// 添加和减少按钮
    $('.addcount').on('click', 'span', function(e){
    	e = e || window.event;
    	var tag = e.target; 
    	switch (tag.className){
    		case 'btnadd':
    			var chickenNum=5;
    			if ($(".ipt").val() > (chickenNum-1)) {

    				$(".ipt").val(chickenNum)
    				layer.msg('阿偶！每次限购'+chickenNum+'只',{
						icon:2,
						time:2000
					});
    			}else{
    				$(".ipt").val(parseInt($(".ipt").val()) + 1);
    			}
    			
    			console.log($(".ipt").val())
    			break;
    		case "btnminus":
    			if ($(".ipt").val() > '1'){
    				$(".ipt").val(parseInt($(".ipt").val()) - 1);
    			}
    			console.log($(".ipt").val())
    			break;
    	}
    	$(".paynum").val(parseFloat(price)*100*parseInt($(".ipt").val())/100)
    });
    $(".pay").on('click',function(){
		datainfo={
			'num':$(".ipt").val(),
	        'order_sn':$(".order_sn").html(),
	        'chicken_batch':$(".chicken_batch").html(),
			'invite_code' : $.cookie('invite_code'),
	        'chicken_type': $.cookie('chicken_type'),
		}
        $.ajax({
            type: 'POST',
            url: 'http://wechat.jiagehao.cn/ShareByChicken/confirmBuyChicken',
            data:datainfo,
            dataType: 'json',
			success:function(msg){
				if(msg.code==1) {
					location.href="<?php echo U('ShareByChicken/weixinpay');?>?order_sn="+msg.result.order_sn;
					//setTimeout("parent.location.reload()", 2000);
				}
				else
				{
					layer.msg('啊偶!'+msg.msg,{
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
</script>
</html>