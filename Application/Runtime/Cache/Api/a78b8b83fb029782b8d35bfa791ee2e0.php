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
                    <li><a href="<?php echo U('AdminUser/index');?>">用户管理</a></li>
                    <li><a href="<?php echo U('AdminWithdrawals/index');?>">提现管理</a></li>
                    <li><a href="<?php echo U('AdminChickenOrder/index');?>">订单管理</a></li>
                    <li><a href="<?php echo U('AdminTodayPrice/index');?>">今日价格</a></li>
                    <li><a href="<?php echo U('AdminNews/index');?>">新闻管理</a></li>
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
        <small>添加新闻</small>
    </div>

    <form method="post" id="create_form">
        <hr>
        <div class="form-group">
            <label>标题:</label>
            <input type="text" name="title" id="title" class="form-control"  placeholder="标题">
        </div>
        <div class="form-group">
            <label>原文链接:</label>
            <input type="text" name="article_url" id="article_url" class="form-control"   placeholder="原文链接">
        </div>
        <div class="form-group">
            <label>摘要:</label>
            <textarea class="form-control" name="abstract" id="abstract"></textarea>
        </div>
        <div class="form-group">
            <label>发布时间:</label>
            <input type="datetime" name="newstime" id="newstime" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD 00:00:00'})" class="layui-input form-control" value="<?php echo ($info["newstime"]); ?>" placeholder="发布时间">
        </div>
        <div class="form-group">
            <label>封面:</label>
            <input type="file" name="news_cover" id="news_cover" value="<?php echo ($info["newstime"]); ?>" placeholder="封面">
        </div>
        <div class="form-group">
            <label>文章来源:</label>
                <?php if(is_array($come_from)): $i = 0; $__LIST__ = $come_from;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$c_vo): $mod = ($i % 2 );++$i; echo ($c_vo); ?><input type="radio" checked name="come_from" value="<?php echo ($key); ?>"><?php endforeach; endif; else: echo "" ;endif; ?>
                <br>
        </div>
        <div class="form-group">
            <label>是否置顶:<input type="checkbox" name="top_num" id="top_num" class="" ><br><small>[选择后该文章将会置顶]</small><br></label>
        </div>
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


        url = "<?php echo U('AdminNews/add');?>"

        var formData = new FormData($( "#create_form" )[0]);

        title       = $( "#title" ).val();
        article_url = $( "#article_url" ).val();
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
</body>
</html>