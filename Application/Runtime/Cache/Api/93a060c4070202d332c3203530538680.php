<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=no, width=device-width">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title>链养鸡-首页</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <script src="/Public/static/layui/layui.js"></script>
    <![endif]-->
    <style>
        .row {
            padding: 30px;
        }
        .row .banner{
            margin-top: 5px;
        }
        .row .btn{
            width: 100%;
            height: 100px;
        }
    </style>
</head>
<body>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<div class="container-fluid">
    <div class=".col-xs-5">
    <h3>链养鸡</h3>
        Hello!<?php echo ($admin_name); ?>
    </div>
    <div class=".col-xs-6">
        <ul class="nav nav-pills">
            <li role="presentation" class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">首页</a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo U('AdminTodayPrice/index');?>">今日价格</a></li>
                    <li><a href="<?php echo U('AdminChickenBatch/index');?>">批次管理</a></li>
                    <li><a href="<?php echo U('AdminChickenbatchTodayfeedDelivery/index');?>">结算管理</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <hr>
</div>
<div class="container-fluid">
    <div class="jumbotron">
        <small>当日结算!所有信息必填</small>
    </div>

    <form method="post" id="create_form">
        <input type="hidden" name="id" value="<?php echo ($info["id"]); ?>" id="id">
        <div class="form-group">
            <label>批次:</label>
            <select name="chicken_batch" id="chicken_batch">
                <option value="">--请选择--</option>
                <?php if(is_array($chicken_batch)): $i = 0; $__LIST__ = $chicken_batch;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if($vo['id'] == $info['chicken_batch']): ?>selected<?php endif; ?>>--<?php echo ($vo["name"]); ?>--</option><?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
        <div class="form-group">
            <label>结算日期:<?php if($info['guess_age_in_days']): ?>今日日龄提示:<?php echo ($info["guess_age_in_days"]); endif; ?></label>
            <input type="datetime" name="delivery_date" value="<?php echo ($info["delivery_date"]); ?>" id="delivery_date" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD'})" class="layui-input form-control" value="<?php echo ($delivery_date); ?>" placeholder="开始时间">
        </div>
        <div class="form-group">
            <label>产蛋数量[个]:</label>
            <input type="number" name="lay_eggs" value="<?php echo ($info["lay_eggs"]); ?>" id="lay_eggs" class="form-control"   placeholder="产蛋数量">
        </div>
        <div class="form-group">
            <label>总蛋重[kg]:</label>
            <input type="number" name="lay_eggs_weight" value="<?php echo ($info["lay_eggs_weight"]); ?>" id="lay_eggs_weight" class="form-control"   placeholder="总蛋重">
        </div>
        <div class="form-group">
            <label>死淘[只]:</label>
            <input type="number" name="death" value="<?php echo ($info["death"]); ?>" id="death" class="form-control"   placeholder="死淘">
        </div>
        <div class="form-group">
            <label>投料[kg]:</label>
            <input type="number" name="feed_weight" value="<?php echo ($info["feed_weight"]); ?>" id="feed_weight" class="form-control"   placeholder="投料">
        </div>
        <div class="form-group">
            <label>现金支出[元]:</label>
            <input type="number" name="expenses" value="<?php echo ($info["expenses"]); ?>" id="expenses" class="form-control"   placeholder="现金支出">
        </div>
        <div class="form-group">
            <label>数字币[枚]:</label>
            <input type="number" name="eggcoin_income" value="<?php echo ($info["eggcoin_income"]); ?>" id="eggcoin_income" class="form-control"   placeholder="数字币">
        </div>
        <button type="button" onclick="edit()" id="tj" class="btn btn-default">提交</button><br><br>
    </form>
</div>
<br>

<script src="/Public/static/layui/layui.js"></script>
<script type="text/javascript">
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


        url = "<?php echo U('AdminChickenbatchTodayfeedDelivery/edit');?>"

        var formData = new FormData($( "#create_form" )[0]);

        id     = $( "#id" ).val();
        chicken_batch     = $( "#chicken_batch" ).val();
        lay_eggs    = $( "#lay_eggs" ).val();
        lay_eggs_weight    = $( "#lay_eggs_weight" ).val();
        death    = $( "#death" ).val();
        feed_weight    = $( "#feed_weight" ).val();
        expenses    = $( "#expenses" ).val();
        delivery_date    = $( "#delivery_date" ).val();
        eggcoin_income    = $( "#eggcoin_income" ).val();
        if(!eggcoin_income || !id || !chicken_batch || !lay_eggs || !lay_eggs_weight || !death || !feed_weight || !expenses || !delivery_date) {
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
                    layer.msg('修改成功!',{
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
</body>
</html>