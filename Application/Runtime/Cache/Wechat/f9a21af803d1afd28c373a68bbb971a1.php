<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
	<meta charset="UTF-8">
	<script type="text/javascript" src="/Public/js/Wechat/resize.js"></script>
	<title>认购母鸡</title>
	<style>
		*{
			margin: 0;
			padding: 0;
		}
		html{
			font-size: 100px;
		}
		.henbuy{
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
		.henbuybox{
			width: 100%;
			height: 13.34rem;
			position: relative;
	        background: #f2f2f2;
		}
	    .banner{
	        width: 100%;
			height: 1.7rem;
	        background: url(../../../../Public/images/Wechat/banner.png) no-repeat;
	    	background-size: 100%;
	    }
	    .type{
	        width: 90%;
	        height: 3.9rem;
	        margin: 5%;
	        background: #fff;
	        border-radius: .1rem;
	        padding: .3rem 0;
	    }
	    .actives{
	        width: 90%;
	        height: 3.9rem;
	        margin: 5%;
	        background: #fff;
	        border-radius: .1rem;
	        padding: .3rem 0;
	        border: 2px solid #6ebc5e;
	    }
	    .type div{
	        width: 100%;
	        height: .75rem;
	        display: flex;
	        align-items: center;
	    }
	    .type i{
	        display: inline-block;
	        width: .75rem;
	        height: .75rem;
	        background: url(../../../../Public/images/Wechat/select.png) no-repeat center;
	    	background-size: 60%;
	    }
	    .active{
	        display: inline-block;
	        width: .75rem;
	        height: .75rem;
	        background: url(../../../../Public/images/Wechat/tick.png) no-repeat center;
	    	background-size: 60%;
	    }
	    .type div span:nth-of-type(1){
	        font-size: .3rem;
	        color: #ff0000;
	        background: #fff;
	        border-radius: .1rem;
	    }
	    .type div span:nth-of-type(2){
	        font-size: .24rem;
	        color: #aaaaaa;
	        margin-right: .2rem;
	        text-decoration: line-through;
	    }
	    .type div span:nth-of-type(3){
	        font-size: .3rem;
	        color: #242424;
	    }
	    .type p{
	        /*width: 100%;*/
	        font-size: .26rem;
	        color: #6b6b6b;
	        padding-left: .75rem;
	        height: .5rem;
	        line-height: .5rem;
	    }

	    .type1{
	        width: 90%;
	        height: 3.9rem;
	        margin: 5%;
	        background: #fff;
	        border-radius: .1rem;
	        padding: .3rem 0;
	    }
	    .type1 div{
	        width: 100%;
	        height: .75rem;
	        display: flex;
	        align-items: center;
	    }
	    .type1 i{
	        display: inline-block;
	        width: .75rem;
	        height: .75rem;
	        background: url(../../../../Public/images/Wechat/select.png) no-repeat center;
	    	background-size: 60%;
	    }
	    .type1 div span:nth-of-type(1){
	        font-size: .3rem;
	        color: #ff0000;
	        background: #fff;
	        border-radius: .1rem;
	    }
	    .type1 div span:nth-of-type(2){
	        font-size: .24rem;
	        color: #aaaaaa;
	        margin-right: .2rem;
	        text-decoration: line-through;
	    }
	    .type1 div span:nth-of-type(3){
	        font-size: .3rem;
	        color: #242424;
	    }
	    .type1 p{
	        /*width: 100%;*/
	        font-size: .26rem;
	        color: #6b6b6b;
	        padding-left: .75rem;
	        height: .5rem;
	        line-height: .5rem;
	    }

	    .button{
	        width: 100%;
	        height: 1rem;
	        position: fixed;
	        bottom: 0;
	        background: #1e92ff;
	        font-size: .3rem;
	        text-align: center;
	        line-height: 1rem;
	        color: #fff;
	    }
	</style>
</head>
<body>
	<div class="henbuy" v-title='"认购母鸡"'>
		<!--<div class="black">认购母鸡</div>-->
		<div class="henbuybox">
			<div class="banner"></div>
            <div class="type">
                <div>
                    <i class="typei"></i>
                    <span>168元/只</span>
                    <span>199元/只</span>
                    <span>母鸡及食物等套餐</span>
                </div>
                <p>·一只真实饲养中的母鸡</p>
                <p>·饲养期内需要的食物</p>
                <p>·饲养期内需要的药物</p>
                <p>·饲养期内需要的水、人工等</p>
                <p>·不受原料价格波动</p>
            </div>
            <div class="type1">
                <div>
                    <i class="type1i"></i>
                    <span>29.9元/只</span>
                    <span>39.9元/只</span>
                    <span>母鸡</span>
                </div>
                <p>·一只真实饲养中的母鸡</p>
                <p>·约饲养至500日龄</p>
                <p>·按照真实养殖数据购买食物</p>
                <p>·按照真实养殖数据支付支出</p>
                <p>·缓解支出压力</p>
            </div>
            <a href="https://itunes.apple.com/app/id=1343684450"></a><div class="button">认养小鸡，真实养殖</div>
		</div>
        
	</div>
</body>
<script type="text/javascript" src="/Public/js/Wechat/jquery-3.2.1.min.js"></script>
<script src="/Public/js/Wechat/jquery.cookie.js"></script>
<script type="text/javascript" charset="utf-8" async defer>
    $.cookie('chicken_type','');
	$(".button").click(function(){
		location.href="<?php echo U('ShareByChicken/pay');?>";
	});
	$(".type").click(function(){
		$(".type1").css("border", "none");
		$(".type1i").css({"background":"url(../../../../Public/images/Wechat/select.png) no-repeat center","background-size":"60%"});
		$(".type").css("border", "2px solid #6ebc5e");
		$(".typei").css({"background":"url(../../../../Public/images/Wechat/tick.png) no-repeat center","background-size":"60%"});
		$.cookie('chicken_type',1,300);
	});
	$(".type1").click(function(){
		$(".type").css("border", "none");
		$(".typei").css({"background":"url(../../../../Public/images/Wechat/select.png) no-repeat center","background-size":"60%"});
		$(".type1").css("border", "2px solid #6ebc5e");
		$(".type1i").css({"background":"url(../../../../Public/images/Wechat/tick.png) no-repeat center","background-size":"60%"});
		$.cookie('chicken_type',2,300);
	});
</script>
</html>