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
                </ul>
            </li>
        </ul>
    </div>

    <hr>
</div>
<div class="container-fluid">
    <a href="<?php echo U('AdminChickenBatch/add');?>">添加今日价格</a><br><br>
    <form class="form-inline" name="searchForm" action="/AdminChickenBatch/index.html" method="get">
        批次名称:<input  type="date" name="name" value="<?php echo ($_GET["name"]); ?>"><br>
        <input type="submit" class="btn btn-success" value="搜索">
    </form>
    共:<?php echo ($count); ?>条!<?php echo ($_page); ?>
    <div class="table-responsive">
        <table class="table">
            <tr>
                <th class="active">批次名称</th>
                <th class="active">鸡舍信息</th>
                <th class="warning">对外批次编码</th>
                <th class="warning">发行数量</th>
                <th class="danger">发行开始时间</th>
                <th class="danger">发行结束时间</th>
                <th class="danger">是否是当前发行</th>
                <th class="active">操作</th>
            </tr>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                    <td class="active" style="padding: 10px"><?php echo ($vo["name"]); ?></td>
                    <td class="active" style="padding: 10px"><?php echo ($vo["coop_name"]); ?><br>[<?php echo ($vo["breed"]); ?>]
                        <?php echo ($vo["egg_in_days"]); ?>天 <?php echo ($vo["egg_color"]); ?><br>预计产蛋:<?php echo ($vo["lay_eggs"]); ?><br><?php echo ($vo["feed_type"]); ?><hr><?php echo ($vo["latitude_longitude"]); ?></td>
                    <td class="warning" style="padding: 10px"><?php echo ($vo["out_code"]); ?></td>
                    <td class="warning" style="padding: 10px"><?php echo ($vo["amount"]); ?></td>
                    <td class="danger" style="padding: 10px"><?php echo ($vo["start_date"]); ?></td>
                    <td class="danger" style="padding: 10px"><?php echo ($vo["end_date"]); ?></td>
                    <td class="danger" style="padding: 10px"><?php echo ($vo["is_default_info"]); ?></td>
                    <td class="active" style="padding: 10px"><a href="<?php echo U('AdminChickenBatch/edit');?>?id=<?php echo ($vo["id"]); ?>">编辑</a></td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </table>
    </div>
    共:<?php echo ($count); ?>条!<?php echo ($_page); ?>
</div>