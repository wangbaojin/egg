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
        <small>添加发行!所有信息必填</small>
    </div>

    <form method="post" id="create_form">
        <h3>鸡舍信息</h3>
        <hr>
        <div class="form-group">
            <label>鸡舍名称:</label>
            <input type="text" name="coop_name" id="coop_name" class="form-control" maxlength="11"  placeholder="鸡舍名称">
        </div>
        <div class="form-group">
            <label>经纬度:</label>
            <input type="text" name="latitude_longitude" id="latitude_longitude" class="form-control"   placeholder="经纬度">
        </div>
        <div class="form-group">
            <label>品种:</label>
            <input type="text" name="breed" id="breed" class="form-control"   placeholder="品种">
        </div>
        <div class="form-group">
            <label>入栏日期:</label>
            <input type="datetime" name="lairage_date" id="lairage_date" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD'})" class="layui-input form-control" value="<?php echo ($delivery_date); ?>" placeholder="入栏日期">
        </div>
        <div class="form-group">
            <label>日龄[相对于入栏日期计算]:</label>
            <input type="text" name="age_in_days" id="age_in_days" class="form-control"   placeholder="日龄">
        </div>
        <div class="form-group">
            <label>预计产蛋:</label>
            <input type="text" name="lay_eggs" id="lay_eggs" class="form-control"   placeholder="预计产蛋">
        </div>
        <div class="form-group">
            <label>产蛋颜色:</label>
            <input type="text" name="egg_color" id="egg_color" class="form-control"   placeholder="产蛋颜色">
        </div>
        <div class="form-group">
            <label>养殖方式:</label>
            <input type="text" name="feed_type" id="feed_type" class="form-control"   placeholder="养殖方式">
        </div>
        <h3>批次信息</h3>
        <hr>
        <div class="form-group">
            <label>批次名称:</label>
            <input type="text" name="name" id="name" class="form-control" maxlength="11"  placeholder="批次名称">
        </div>
        <div class="form-group">
            <label>对外批次编码:</label>
            <input type="text" name="out_code" id="out_code" class="form-control"   placeholder="对外批次编码">
        </div>
        <div class="form-group">
            <label>发行数量(1~999999):</label>
            <input type="number" name="amount" id="amount" class="form-control"   placeholder="发行数量">
        </div>
        <div class="form-group">
            <label>认购开始时间:</label>
            <input type="datetime" name="start_time" id="start_time" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD 00:00:00'})" class="layui-input form-control" value="<?php echo ($start_time); ?>" placeholder="开始时间">
        </div>
        <div class="form-group">
            <label>认购结束时间[不可小于认购开始时间]:</label>
            <input type="datetime" name="end_time" id="end_time" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD 00:00:00'})" class="layui-input form-control" value="<?php echo ($end_time); ?>" placeholder="结束时间">
        </div>
        <div class="form-group">
            <label>是否设为当前默认发行批次:<input type="checkbox" name="is_default" id="is_default" class="" ><br><small>[选择后当前发行批次会变为该添加批次]</small><br></label>
        </div>
        <font color="red">注意⚠!发行规则:<br>批次状态为正常,是当前默认发行,且当前时间在发行时间之间用户方可认购<br><br></font>
        <button type="button" onclick="add()" id="tj" class="btn btn-default">提交</button><br><br>
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
        if(!lairage_date || !out_code || !start_time || !end_time || !coop_name || !latitude_longitude || !breed || !age_in_days || !lay_eggs || !egg_color || !feed_type || !name || !amount) {
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
</body>
</html>