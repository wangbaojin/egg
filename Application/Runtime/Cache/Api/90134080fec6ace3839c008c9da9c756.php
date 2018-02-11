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
    <a href="<?php echo U('AdminChickenbatchTodayfeedDelivery/add');?>">添加结算</a><br><br>
    <form class="form-inline" name="searchForm" action="/AdminChickenbatchTodayfeedDelivery/index.html" method="get">
        批次名称:<input  type="date" name="delivery_date" value="<?php echo ($_GET["delivery_date"]); ?>"><br>
        <input type="submit" class="btn btn-success" value="搜索">
    </form>
    共:<?php echo ($count); ?>条!<?php echo ($_page); ?>
    <div class="table-responsive">
        <table class="table">
            <tr>
                <th class="active" style="width: 100px">批次名称</th>
                <th class="active">鸡蛋</th>
                <th class="danger">饲料</th>
                <th class="active">现金支出</th>
                <th class="active">数字资产发行</th>
                <th class="active">操作</th>
            </tr>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                    <td class="active" style="padding: 10px"><?php echo ($vo["chicken_batch_name"]); ?><hr><?php echo ($vo["delivery_date"]); ?></td>
                    <td class="active" style="padding: 10px">
                        收取:<?php echo ($vo["lay_eggs"]); ?> 枚<br>
                        共重:<?php echo ($vo["lay_eggs_weight"]); ?> kg<br>
                        收益:<?php echo ($vo["egg_price"]); ?> 元<br>
                        死淘:<?php echo ($vo["death"]); ?> 只<br>
                        结算日龄:<?php echo ($vo["age_in_days"]); ?> 日<br>
                    </td>
                    <td class="danger" style="padding: 10px">
                        消耗:<?php echo ($vo["feed_weight"]); ?> kg<br>
                        共计:<?php echo ($vo["feed_price"]); ?> 元<br>
                    </td>
                    <td class="active" style="padding: 10px"><?php echo ($vo["expenses"]); ?>元</td>
                    <td class="active" style="padding: 10px"><?php echo ($vo["eggcoin_income"]); ?>枚</td>
                    <td class="active" style="padding: 10px">

                        <a href="<?php echo U('AdminChickenbatchTodayfeedDelivery/edit');?>?id=<?php echo ($vo["id"]); ?>">编辑</a><br><br>
                        <?php if($vo['state'] == 1): ?><a onclick=confimDelivery("<?php echo U('AdminChickenbatchTodayfeedDelivery/confimDelivery');?>?id=<?php echo ($vo["id"]); ?>")>确认结算</a>
                            <?php elseif($vo['state'] == 2): ?>
                                已开始结算
                            <?php else: ?>
                                结算完成<?php endif; ?>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </table>
    </div>
    共:<?php echo ($count); ?>条!<?php echo ($_page); ?>
</div>
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

    function confimDelivery(url){


        //url = "<?php echo U('AdminChickenbatchTodayfeedDelivery/confimDelivery');?>"

        $.ajax({
            type: 'get',
            url: url,
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