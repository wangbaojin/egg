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
    <form class="form-inline" name="searchForm" action="/AdminUser/index.html" method="get">
        手机号:<input  type="text" name="mobile" value="<?php echo ($_GET["mobile"]); ?>"><br>
        <input type="submit" class="btn btn-success" value="搜索">
    </form>
    共:<?php echo ($count); ?>条!<?php echo ($page); ?>
    <div class="table-responsive">
        <table class="table">
            <tr>
                <th class="active">用户基本信息</th>
                <th class="active">微信信息</th>
                <th class="warning">微博信息</th>
                <th class="active">操作</th>
            </tr>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                    <td class="active" style="padding: 10px">
                        <img src="<?php echo ($vo["pic"]); ?>" width="100px"><br>
                        ID:<?php echo ($vo["id"]); ?>&nbsp; <?php echo ($vo["full_name"]); ?><br>
                        <small><?php echo ($vo["vip_info"]); ?> [<?php echo ($vo["created_date"]); ?>]</small><br>
                        <?php echo ($vo["mobile"]); ?> &nbsp<br>
                        <?php echo ($vo["email"]); echo ($vo["email_status_info"]); ?>
                    </td>
                    <td class="active" style="padding: 10px">
                        <img src="<?php echo ($vo["wechart_info"]["wx_pic"]); ?>" width="100px"><br>
                        <?php echo ($vo["wechart_info"]["wx_nick_name"]); ?><br>
                        <?php echo ($vo["wechart_info"]["country"]); ?> &nbsp;<br>
                        <?php echo ($vo["wechart_info"]["sex_info"]); ?>
                    </td>
                    <td class="active" style="padding: 10px">暂无
                    </td>
                    <td class="active" style="padding: 10px">
                        <a href="">编辑</a><br><br>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </table>
    </div>
    共:<?php echo ($count); ?>条!<?php echo ($page); ?>
</div>