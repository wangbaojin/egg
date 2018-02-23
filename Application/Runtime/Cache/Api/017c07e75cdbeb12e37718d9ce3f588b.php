<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=no, width=device-width">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title>快乐de蛋-用户登录</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<div class="container-fluid">
    <div class="jumbotron">
        <h6>渠道登录</h6>
        <small>登录之前需由管理员开通帐户</small>
    </div>

    <form method="post" id="create_form">
        <div class="form-group">
            <label for="exampleInputEmail1">账号(手机号):</label>
            <input type="text" name="mobile" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" class="form-control" maxlength="11" id="exampleInputPassword1" placeholder="Name">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">密码:</label>
            <input type="password" name="pass_wd" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" class="form-control" maxlength="11" id="exampleInputPassword1" placeholder="Password">
        </div>
        <button type="button" onclick="add()" class="btn btn-default">提交</button>
    </form>
</div>
<br>

<script src="/Public/static/layui/layui.js"></script>
<script type="text/javascript">
    layui.use('layer', function(){ //独立版的layer无需执行这一句
        //var $ = layui.jquery,
        layer = layui.layer; //独立版的layer无需执行这一句
    });

    function add(){

        setTimeout(function(){
            url = "<?php echo U('AdminLogin/login');?>"

            var formData = new FormData($( "#create_form" )[0]);

            mobile = $('input[name=mobile]').val();
            pass_wd = $('input[name=pass_wd]').val();
            if(!mobile || !pass_wd ) {
                layer.msg('程序员小哥哥提示你:账号密码都不填你还想登录?是我拿不动刀了还是你飘了?',{
                    icon:2,
                    time:2000
                });return false;
            }

            $.ajax({
                type: 'POST',
                url: url,
                data:formData,
                processData: false,
                contentType: false,
                dataType: 'text',
                success:function(msg){
                    if(msg=='success') {
                        layer.msg('登录成功!',{
                            icon:1,
                            time:2000
                        });
                        document.location = "<?php echo U('AdminIndex/index');?>";
                    }
                    else
                    {
                        layer.msg('程序员小哥哥提示你:账号密码错了!',{
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
        },100)
    }
</script>
</body>
</html>